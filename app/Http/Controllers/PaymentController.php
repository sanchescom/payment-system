<?php

namespace App\Http\Controllers;

use App\Entities\Secret;
use App\Exceptions\SystemErrorException;
use App\Jobs\PaymentProcess;
use App\Payment;
use App\Services\CurrencyConverter;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentController extends BaseController
{
    public function transfer(Request $request, CurrencyConverter $converter)
    {
        $this->validatePayment($request);

        $currency = $request->get('currency');
        $amount   = $request->get('amount');

        $reserved = $converter->convert(Carbon::now(), $currency, $amount);

        $user = $this->getCurrentUser();

        $this->checkSecret($request, $user);
        $this->checkFounds($reserved, $user);

        try
        {
            $payment = new Payment();
            $payment->fill($request->all());
            $payment->setProcessingStatus();
            $payment->setSpendDirection();
            $payment->save();

            $user->increaseReserved($reserved);

            PaymentProcess::dispatch($payment);
        }
        catch (\Exception $exception)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Transaction failed', $exception);
        }

        return response()->json([
            'data' => $payment->toArray(),
        ], Response::HTTP_OK);
    }


    public function recharge(Request $request)
    {
        $this->validatePayment($request);

        try
        {
            $payment = new Payment();
            $payment->date = Carbon::now()->toDateString();
            $payment->fill($request->all());
            $payment->setProcessingStatus();
            $payment->setIncomeDirection();
            $payment->save();

            PaymentProcess::dispatch($payment);
        }
        catch (\Exception $exception)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Unsuccessful recharging', $exception);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


    private function checkSecret(Request $request, User $user)
    {
        if (Secret::hash($request->get('secret')) !== $user->secret)
        {
            throw new AccessDeniedHttpException('Access denied');
        }
    }


    private function checkFounds($reserved, User $user)
    {
        if ($reserved > $user->amount)
        {
            throw new SystemErrorException('Insufficient funds');
        }
    }


    private function validatePayment(Request $request)
    {
        $this->validate($request, [
            'payee'    => 'required|max:255|exists:users,email',
            'amount'   => 'required|max:4',
            'currency' => 'max:3',
        ]);
    }
}