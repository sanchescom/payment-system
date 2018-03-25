<?php

namespace App;

use App\Collections\PaymentsCollection;
use Carbon\Carbon;

/**
 * Class Payment
 *
 * @property int $id
 * @property string $payee
 * @property string $currency
 * @property double $amount
 * @property string $payer
 * @property int $status
 * @property int $type
 * @property Carbon $date
 * @property double $native
 * @property double $default
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
        'amount',
        'currency',
    ];

    protected $guarded = [
        'type',
        'payer',
        'status',
        'direction',
        'native',
        'default',
    ];

    protected $dates = [
        'date'
    ];


    public function newCollection(array $models = [])
    {
        return new PaymentsCollection($models);
    }


    public function setProcessingStatus()
    {
        $this->status = self::PROCESSING_STATUS;
    }


    public function setSuccessStatus()
    {
        $this->status = self::SUCCESSFUL_STATUS;
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


    public function setPayer($payer)
    {
        $this->payer = $payer;
    }


    public function setNative($native)
    {
        $this->native = $native;
    }


    public function setDefault($default)
    {
        $this->default = $default;
    }


    public function getNativeAttribute()
    {
        return $this->attributeGetterConverterIntegerToDouble('native');
    }


    public function setNativeAttribute($value)
    {
        $this->attributeSetterConverterDoubleToInteger('native', $value);
    }


    public function getDefaultAttribute()
    {
        return $this->attributeGetterConverterIntegerToDouble('default');
    }


    public function setDefaultAttribute($value)
    {
        $this->attributeSetterConverterDoubleToInteger('default', $value);
    }
}