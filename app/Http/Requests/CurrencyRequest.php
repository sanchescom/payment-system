<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RechargeAccountRequest.
 *
 * @property array $currencies
 */
class CurrencyRequest extends FormRequest
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
            'currencies.*.date'     => 'required|date',
            'currencies.*.rate'     => 'required|numeric',
            'currencies.*.currency' => 'required|max:3',
        ];
    }
}
