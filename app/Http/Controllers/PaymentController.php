<?php

namespace App\Http\Controllers;

use App\Entities\Secret;
use App\Exceptions\SystemErrorException;
use App\Jobs\PaymentIncome;
use App\Jobs\PaymentSpend;
use App\Payment;
use App\Services\AccountProcessor;
use App\Services\CurrencyConverter;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentController extends BaseController
{
    public function transfer(Request $request, CurrencyConverter $converter, AccountProcessor $processor)
    {
        $this->validatePayment($request);

        $user = $this->getCurrentUser();

        $currency = $request->get('currency');
        $amount   = $request->get('amount');
        $payee    = $request->get('payee');

        $this->validate($request, [
            'currency' => 'in:' . $user->currency . ',' . $processor->getCurrency($payee)
        ]);

        if ($user->currency != $currency)
        {
            $amount = $converter->convert(Carbon::now(), $currency . "/" . $currency, $amount);
        }

        $this->checkSecret($request, $user);
        $this->checkFounds($amount, $user);

        try
        {
            $payment = new Payment();
            $payment->fill($request->all());
            $payment->setDate(Carbon::now()->toDateString());
            $payment->setPayer($user->getAccount());
            $payment->setProcessingStatus();
            $payment->setSpendDirection();
            $payment->save();

            $user->decreaseAmount($amount);

            PaymentSpend::dispatch($payment);
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
            $payment->fill($request->all());
            $payment->setDate(Carbon::now()->toDateString());
            $payment->setProcessingStatus();
            $payment->setIncomeDirection();
            $payment->save();

            PaymentIncome::dispatch($payment);
        }
        catch (\Exception $exception)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Unsuccessful recharging', $exception);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


    private function checkSecret(Request $request, User $user)
    {
        if (Secret::hash($request->get('secret')) !== $user->getSecret())
        {
            throw new AccessDeniedHttpException('Access denied');
        }
    }


    private function checkFounds($amount, User $user)
    {
        if ($amount > $user->amount)
        {
            throw new SystemErrorException('Insufficient funds');
        }
    }


    private function validatePayment(Request $request)
    {
        $this->validate($request, [
            'payee'    => 'required|max:14|exists:users,account',
            'amount'   => 'required|numeric',
            'currency' => 'required|max:3',
        ]);
    }
}