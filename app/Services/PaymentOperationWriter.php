<?php

namespace App\Services;

use App\Currency;
use App\Entities\PaymentOperation;
use App\Payment;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

/**
 * Class PaymentOperation.
 */
class PaymentOperationWriter extends Writer
{
    /**
     * PaymentOperation constructor.
     * @param $document
     */
    public function __construct($document)
    {
        parent::__construct($document);
    }

    /**
     * @param PaymentOperation $paymentOperation
     */
    public function insertPaymentOperation(PaymentOperation $paymentOperation)
    {
        $this->insertAll($paymentOperation->getPayments()->getDataForCsv($paymentOperation->getUser())->toArray());
        try {
            $this->insertOne([
                Currency::DEFAULT_CURRENCY . ':' . $paymentOperation->getSummaries()->getNativeAndDefaultSum()[Payment::DEFAULT_DYNAMIC_SUM_FIELD]
            ]);
            $this->insertOne([
                $paymentOperation->getUser()->currency . ':' . $paymentOperation->getSummaries()->getNativeAndDefaultSum()[Payment::NATIVE_DYNAMIC_SUM_FIELD]
            ]);
        } catch (CannotInsertRecord $e) {
            //
        }
    }
}