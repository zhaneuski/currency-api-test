<?php

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalculateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Response $response)
    {
        $parameters = $request->request->all();

        if (empty($parameters['value']) || empty($parameters['currencyFrom']) || empty($parameters['currencyTo'])) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid request parameters',
            ]);
        }

        $actualCurrencies = config('currencies.actual');

        if (!in_array($parameters['currencyFrom'], $actualCurrencies) || !in_array($parameters['currencyTo'], $actualCurrencies)) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid currency type',
            ]);
        }

        $currency = Currency::latest()->first();
        $data = json_decode($currency->data, true);
        $data = array_combine(array_column($data, 'slug'), $data);

        return response()->json($this->calculate($parameters, $data));
    }

    private function calculate(array $parameters, array $data): array
    {
        $value = floatval($parameters['value']);
        $currencyFrom = strtoupper($parameters['currencyFrom']);
        $currencyTo = strtoupper($parameters['currencyTo']);

        // Приводим значение валюты, переданное в запросе, к общему формату в долларах.
        $valueDollars = $value / floatval($data[$currencyFrom]['value']);

        $resultValue = $valueDollars * floatval($data[$currencyTo]['value']);

        return [
            'value' => $resultValue,
            'slug' => $currencyTo,
        ];
    }
}
