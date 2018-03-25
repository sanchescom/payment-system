<?php

namespace App\Http\Controllers;

use App\Entities\Secret;
use App\Exceptions\SystemErrorException;
use App\Jobs\PaymentIncome;
use App\Jobs\PaymentSpend;
use App\Payment;
use App\Repositories\PaymentRepository;
use App\Services\AccountProcessor;
use App\Services\CurrencyConverter;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentController extends Controller
{
    public function transfer(Request $request, CurrencyConverter $converter, AccountProcessor $processor)
    {
        $this->validatePayment($request);

        $user = $this->getCurrentUser();

        $currency = $request->get('currency');
        $amount   = $request->get('amount');
        $payee    = $request->get('payee');

        $this->validate($request, [
            'currency' => 'in:' . $user->currency . ',' . $processor->getCurrency($payee),
            'payee'    => 'not_in:' . $user->getAccount(),
        ]);

        $converted = $converter->convert(Carbon::now(), $currency . "/" . $user->currency, $amount);

//        $this->checkSecret($request, $user);
        $this->checkFounds($converted, $user);

        try
        {
            $payment = new Payment();
            $payment->fill($request->all());
            $payment->setDate(Carbon::now()->toDateString());
            $payment->setPayer($user->getAccount());
            $payment->setProcessingStatus();
            $payment->setSpendDirection();
            $payment->save();

            $user->decreaseAmount($converted);

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


    public function operations(Request $request)
    {
        $this->validate($request, [
            'account'   => 'required|max:14',
            'from_date' => 'date',
            'to_date'   => 'date',
        ]);

        try
        {
            $account   = $request->get('account');
            $from_date = $request->get('from_date');
            $to_date   = $request->get('to_date');

            $user      = User::findByAccount($account);
            $payments  = PaymentRepository::getForUserByPeriod($user, $from_date, $to_date);

            $sum = PaymentRepository::getSumForUserByPeriodGroupedByCurrencies($user, $from_date, $to_date);
        }
        catch (\Exception $exception)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Problem with viewing list operations', $exception);
        }

        return response()->json([
            'data' => $payments->toArray(),
            'meat' => [
                'user' => $user->getData(),
                'sum'  => $sum,
            ],
        ], Response::HTTP_OK);
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