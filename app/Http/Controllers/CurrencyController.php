<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CurrencyController extends Controller
{
    public function upload(Request $request)
    {
        $this->validate($request, [
            'currencies.*.date'     => 'required|date',
            'currencies.*.rate'     => 'required|numeric',
            'currencies.*.currency' => 'required|max:3',
        ]);

        try
        {
            $currencies = $request->get('currencies');

            foreach ($currencies as $currency)
            {
                Currency::query()->updateOrCreate($currency);
            }
        }
        catch (\Exception $exception)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Currencies uploading failed', $exception);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}