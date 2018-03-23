<?php

namespace App;
use App\Collections\PaymentsCollection;
use App\Http\Controllers\PaymentController;
use Carbon\Carbon;

/**
 * Class Payment
 *
 * @property int $id
 * @property string $payee
 * @property string $currency
 * @property int $amount
 * @property string $payer
 * @property int $status
 * @property int $type
 * @property Carbon $date
 *
 * @property User $payee_user
 * @property User $payer_user
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

    protected $fillable = [
        'payee',
        'currency',
        'amount',
        'direction',
    ];

    protected $guarded = [
        'payer',
        'status'
    ];

    protected $dates = [
        'date'
    ];


    public function payee_user()
    {
        return $this->belongsTo(User::class, 'payee', 'email');
    }


    public function payer_user()
    {
        return $this->belongsTo(User::class, 'payer', 'email');
    }


    public function newCollection(array $models = [])
    {
        return new PaymentsCollection($models);
    }


    public function setProcessingStatus()
    {
        $this->status = self::PROCESSING_STATUS;
    }


    public function setIncomeDirection()
    {
        $this->type = self::INCOME_DIRECTION;
    }


    public function setSpendDirection()
    {
        $this->type = self::SPEND_DIRECTION;
    }


    public function setDate($date)
    {
        $this->date = $date;
    }
}