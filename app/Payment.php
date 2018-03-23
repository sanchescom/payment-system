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
 * @property string $status
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

    protected $fillable = [
        'payee',
        'currency',
        'amount',
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
}