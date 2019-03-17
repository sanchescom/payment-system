<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RechargeAccountRequest
 * @property string $account
 * @property string $from_date
 * @property string $to_date
 * @package App\Http\Requests
 */
class PaymentOperationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account'   => 'required|max:14',
            'from_date' => 'date',
            'to_date'   => 'date',
        ];
    }

    /**
     * @return string
     */
    public function getAccount(): string
    {
        return $this->account;
    }

    /**
     * @return Carbon|null
     */
    public function getFromDate():? Carbon
    {
        if ($this->from_date) {
            return Carbon::createFromFormat('Y-m-d', $this->from_date);
        }

        return null;
    }

    /**
     * @return Carbon|null
     */
    public function getToDate(): string
    {
        if ($this->to_date) {
            return Carbon::createFromFormat('Y-m-d', $this->to_date);
        }

        return null;
    }
}
