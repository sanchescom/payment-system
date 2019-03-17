<?php

namespace App\Http\Controllers;

use App\Collections\PaymentsCollection;
use App\Currency;
use App\Entities\Secret;
use App\Exceptions\PaymentOperationsFailed;
use App\Exceptions\RechargeAccountFailed;
use App\Exceptions\SystemErrorException;
use App\Exceptions\TransferMoneyFailed;
use App\Http\Requests\PaymentOperationsRequest;
use App\Http\Requests\RechargeAccountRequest;
use App\Http\Resources\PaymentResource;
use App\Jobs\PaymentIncome;
use App\Jobs\PaymentSpend;
use App\Http\Requests\TransferMoneyRequest;
use App\Payment;
use App\Repositories\PaymentRepository;
use App\Services\CurrencyConverter;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentController extends Controller
{
    /**
     * @param TransferMoneyRequest $request
     * @param CurrencyConverter $converter
     * @param Payment $payment
     * @param Carbon $carbon
     * @return PaymentResource
     */
    public function transferMoney(
        TransferMoneyRequest $request,
        CurrencyConverter $converter,
        Payment $payment,
        Carbon $carbon
    ) {
        $converted = $converter->convert(
            $carbon,
            $request->getCurrencyPair(),
            $request->getAmount()
        );

        $this->checkSecret($request);
        $this->checkFounds($request, $converted);

        try {
            $payment->fill($request->all());
            $payment->setDate($carbon->toDateString());
            $payment->setPayer($request->user()->getAccount());
            $payment->setProcessingStatus();
            $payment->setSpendDirection();
            $payment->save();

            $request->user()->decreaseAmount($converted);

            PaymentSpend::dispatch($payment);
        } catch (\Exception $exception) {
            throw new TransferMoneyFailed($exception);
        }

        return PaymentResource::make($payment);
    }

    /**
     * @param RechargeAccountRequest $request
     * @param Payment $payment
     * @param Carbon $carbon
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechargeAccount(
        RechargeAccountRequest $request,
        Payment $payment,
        Carbon $carbon
    ) {
        try {
            $payment->fill($request->all());
            $payment->setDate($carbon->toDateString());
            $payment->setProcessingStatus();
            $payment->setIncomeDirection();
            $payment->save();

            PaymentIncome::dispatch($payment);
        } catch (\Exception $exception) {
            throw new RechargeAccountFailed($exception);
        }

        return response()->noContent();
    }

    public function getAllOperations(PaymentOperationsRequest $request)
    {
        /**
         * @var User $user
         * @var PaymentsCollection|Payment[] $payments
         * @var PaymentsCollection|Payment[] $sums
         */
        list($user, $payments, $sums) = $this->getOperations($request);

        return response()->json(
            [
            'data' => $payments->getData(),
            'meta' => [
                'user' => $user->getData(),
                'sums' => $sums->getNativeAndDefaultSum(),
            ],
            ],
            Response::HTTP_OK
        );
    }

    public function downloadAllOperations(PaymentOperationsRequest $request)
    {
        /**
         * @var User $user
         * @var PaymentsCollection|Payment[] $payments
         * @var PaymentsCollection|Payment[] $sums
         */
        list($user, $payments, $sums) = $this->getOperations($request);

        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        try {
            $csv->insertAll($payments->getDataForCsv($user)->toArray());
            $csv->insertOne([
                Currency::DEFAULT_CURRENCY . ':' . $sums->getNativeAndDefaultSum()[Payment::DEFAULT_DYNAMIC_SUM_FIELD]
            ]);
            $csv->insertOne([
                $user->currency . ':' . $sums->getNativeAndDefaultSum()[Payment::NATIVE_DYNAMIC_SUM_FIELD]
            ]);
        } catch (CannotInsertRecord | \TypeError $e) {
            $e->getMessage();
        }

        return response()->make(
            (string) $csv,
            200,
            [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="payments.csv"',
            ]
        );
    }

    /**
     * @param PaymentOperationsRequest $request
     * @return array
     */
    private function getOperations(PaymentOperationsRequest $request)
    {
        try {
            $user = User::findByAccount($request->getAccount());

            return [
                $user,
                PaymentRepository::getAllForUserOnPeriod(
                    $user,
                    $request->getFromDate(),
                    $request->getToDate()
                ),
                PaymentRepository::getSummariesForUserOnPeriod(
                    $user,
                    $request->getFromDate(),
                    $request->getToDate()
                ),
            ];
        } catch (\Exception $exception) {
            throw new PaymentOperationsFailed($exception);
        }
    }

    /**
     * @param TransferMoneyRequest $request
     */
    private function checkSecret(TransferMoneyRequest $request)
    {
        if (Secret::hash($request->getSecret()) !== $request->user()->getSecret()) {
            throw new AccessDeniedHttpException('Access denied');
        }
    }

    /**
     * @param TransferMoneyRequest $request
     * @param $amount
     */
    private function checkFounds(TransferMoneyRequest $request, $amount)
    {
        if ($amount > $request->user()->amount) {
            throw new SystemErrorException('Insufficient funds');
        }
    }
}
