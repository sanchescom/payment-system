<?php

namespace App;
use App\Collections\PaymentsCollection;
use App\Http\Controllers\PaymentController;

/**
 * Class Payment
 *
 * @property int $id
 * @property string $payee
 * @property string $currency
 * @property int $amount
 * @property int $user_id
 * @property int $status
 * @property int $type
 *
 * @property User $user
 *
 * @package App
 */
class Payment extends BaseModel
{
    const PROCESSING_STATUS = 1;
    const SUCCESSFUL_STATUS = 2;
    const FAILED_STATUS = 3;

    const INCOME_DIRECTION = 1;
    const SPEND_DIRECTION = 2;

    const MONEY_INCOME_PAYMENT_MESSAGE = "";

    protected $fillable = [
        'payee',
        'currency',
        'amount',
        "type",
        'direction',
    ];

    protected $guarded = [
        'user_id',
        'status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function newCollection(array $models = [])
    {
        return new PaymentsCollection($models);
    }


    public function setProcessingStatus()
    {
        $this->status = self::PROCESSING_STATUS;
    }


    public function setIncomeType()
    {
        $this->type = self::INCOME_TYPE;
    }


    public function setSpendType()
    {
        $this->type = self::SPEND_TYPE;
    }
}