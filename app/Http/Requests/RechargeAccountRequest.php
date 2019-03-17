<?php

namespace App\Http\Requests;

use App\Services\AccountProcessor;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RechargeAccountRequest
 * @package App\Http\Requests
 */
class RechargeAccountRequest extends FormRequest
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
            'payee'    => 'required|max:14|exists:users,account',
            'amount'   => 'required|numeric',
            'currency' => 'required|max:3',
        ];
    }
}
