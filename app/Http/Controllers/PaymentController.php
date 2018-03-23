<?php

namespace App\Http\Controllers;

use App\Entities\Secret;
use App\Jobs\PaymentProcess;
use App\Payment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentController extends BaseController
{
    public function transfer(Request $request)
    {
        $this->validatePayment($request);

        $user = $this->getCurrentUser();
        $this->checkSecret($request, $user);

        try
        {
            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->fill($request->all());


            PaymentProcess::dispatch();
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

        }
        catch (\Exception $exception)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Unsuccessful recharging', $exception);
        }

        return response()->json([
        ], Response::HTTP_OK);
    }


    private function checkSecret(Request $request, User $user)
    {
        if (Secret::hash($request->get('secret')) !== $user->secret)
        {
            throw new AccessDeniedHttpException('Access denied');
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