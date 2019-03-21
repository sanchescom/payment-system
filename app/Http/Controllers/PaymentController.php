<?php

namespace App\Http\Controllers;

use App\Entities\PaymentOperation;
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
use App\Services\PaymentOperationWriter;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PaymentController extends Controller
{
    /**
     * @param TransferMoneyRequest $request
     * @param CurrencyConverter $converter
     * @param Payment $payment
     * @param Carbon $carbon
     *
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
     *
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
        $paymentOperations = $this->getOperations($request);

        return response()->json(
            [
            'data' => $paymentOperations->getPayments()->getData(),
            'meta' => [
                'user' => $paymentOperations->getUser()->getData(),
                'sums' => $paymentOperations->getSummaries()->getNativeAndDefaultSum(),
            ],
            ],
            Response::HTTP_OK
        );
    }

    public function downloadAllOperations(
        PaymentOperationsRequest $operationsRequest,
        PaymentOperationWriter $operationWriter
    ) {
        $csv = '';

        try {
            $csv = $operationWriter->insertPaymentOperation(
                $this->getOperations($operationsRequest)
            );
        } catch (\TypeError $e) {
            $e->getMessage();
        }

        return response()
            ->make((string) $csv, Response::HTTP_OK)
            ->withHeaders([
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="payments.csv"',
            ]);
    }

    /**
     * @param PaymentOperationsRequest $request
     * @return PaymentOperation
     */
    private function getOperations(PaymentOperationsRequest $request)
    {
        try {
            $user = User::findByAccount($request->getAccount());

            return PaymentOperation::make(
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
                )
            );
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
