<?php

namespace App\Http\Requests;

use App\Services\AccountProcessor;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Payment
 *
 * @property string $currency
 * @property double $amount
 * @property string $payee
 * @property string $secret
 * @method User user($guard = null)
 *
 * @package App\Http\Requests
 */
class TransferMoneyRequest extends FormRequest
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
        /** @var AccountProcessor $accountProcessor */
        $accountProcessor = resolve('AccountProcessor');

        $userCurrency = $this->user()->currency;
        $payeeCurrency = $accountProcessor->getCurrency($this->payee);

        return [
            'payee'    => 'required|max:14|exists:users,account|not_in:' . $this->user()->getAccount(),
            'amount'   => 'required|numeric',
            'currency' => 'required|max:3|in:' . $userCurrency . ',' . $payeeCurrency,
        ];
    }
}
