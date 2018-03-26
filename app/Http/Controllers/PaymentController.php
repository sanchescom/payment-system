<?php

namespace App\Http\Controllers;

use App\Collections\PaymentsCollection;
use App\Currency;
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
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentController extends Controller
{
    public function transferMoney(Request $request, CurrencyConverter $converter, AccountProcessor $processor)
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

        $this->checkSecret($request, $user);
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


    public function rechargeAccount(Request $request)
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


    public function getAllOperations(Request $request)
    {
        /**
         * @var User $user
         * @var PaymentsCollection|Payment[] $payments
         * @var PaymentsCollection|Payment[] $sums
         */
        list($user, $payments, $sums) = $this->getOperations($request);

        return response()->json([
            'data' => $payments->getData(),
            'meta' => [
                'user' => $user->getData(),
                'sums' => $sums->getNativeAndDefaultSum(),
            ],
        ], Response::HTTP_OK);
    }


    public function downloadAllOperations(Request $request)
    {
        /**
         * @var User $user
         * @var PaymentsCollection|Payment[] $payments
         * @var PaymentsCollection|Payment[] $sums
         */
        list($user, $payments, $sums) = $this->getOperations($request);

        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        try
        {
            $csv->insertAll($payments->getDataForCsv($user)->toArray());
            $csv->insertOne([Currency::DEFAULT_CURRENCY .':' . $sums->getNativeAndDefaultSum()[Payment::DEFAULT_DYNAMIC_SUM_FIELD]]);
            $csv->insertOne([$user->currency . ':' . $sums->getNativeAndDefaultSum()[Payment::NATIVE_DYNAMIC_SUM_FIELD]]);
        }
        catch (CannotInsertRecord | \TypeError $e)
        {
            $e->getMessage();
        }

        return response()->make((string) $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="payments.csv"',
        ]);
    }


    private function getOperations(Request $request)
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

            $user = User::findByAccount($account);

            if ($from_date)
            {
                $from_date = Carbon::createFromFormat('Y-m-d', $from_date);
            }

            if ($to_date)
            {
                $to_date = Carbon::createFromFormat('Y-m-d', $to_date);
            }

            return [
                $user,
                PaymentRepository::getForUserByPeriod($user, $from_date, $to_date),
                PaymentRepository::getSumForUserByPeriodGroupedByCurrencies($user, $from_date, $to_date)
            ];
        }
        catch (\Exception $exception)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Problem with viewing list operations', $exception);
        }
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