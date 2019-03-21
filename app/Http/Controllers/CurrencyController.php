<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Exceptions\CurrencyUploadingError;
use App\Http\Requests\CurrencyRequest;
use Illuminate\Http\Response;

/**
 * Class CurrencyController.
 */
class CurrencyController extends Controller
{
    /**
     * @param CurrencyRequest $request
     *
     * @return Response
     */
    public function uploadRates(CurrencyRequest $request)
    {
        try {
            foreach ($request->currencies as $currency) {
                Currency::query()->updateOrCreate($currency);
            }
        } catch (\Exception $exception) {
            throw new CurrencyUploadingError($exception);
        }

        return response()->noContent();
    }
}
