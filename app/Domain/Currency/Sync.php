<?php

namespace App\Domain\Currency;

use App\Models\Currency;
use App\Models\Log;

class Sync
{
    private string $url = 'https://api.freecurrencyapi.com';
    private string $method = '/v1/latest';
    private string $apiKey = 'fca_live_TDayWgLgqXFnWCUQtT5t9fuwmnjXB6f5MSjzXBlk';

    public function __invoke()
    {
        $response = $this->curl([
            'url' => $this->url,
            'method' => $this->method,
            'httpQuery' => [
                'apikey' => $this->apiKey
            ],
        ]);

        if (!empty($response['error'])) {
            $this->log('sync_currency_error', [
                'date' => time(),
                'message' => $response['message'] ?? '',
            ]);
        }

        $currencies = $this->formatCurrencyData($response['data'] ?? []);

        if (empty($currencies)) {
            $this->log('sync_currency_empty', [
                'date' => time(),
            ]);
        }

        $currency = new Currency;  // Создаем экземпляр модели где хранится валюта
        $currency->data = json_encode($currencies, JSON_UNESCAPED_UNICODE);
        $save = $currency->save(); // сохраняем в базу результаты

        // Если сохранение в базу произошло успешно, то выводим соотв. сообщение
        if (!$save) {

            $this->log('sync_currency_save_db_error', [
                'date' => time(),
                'message' => 'При сохранении валют в БД что-то пошло не так',
            ]);
        }

        $this->log('sync_currency_save_db_success', [
            'date' => time(),
            'message' => 'Синхронизация валют прошла успешно!',
        ]);
    }

    private function log(string $type, array $data)
    {
        $currency = new Log();  // Создаем экземпляр модели где хранится валюта
        $currency->data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $currency->type = $type;
        $currency->save(); // сохраняем в базу результаты
    }

    private function formatCurrencyData(array $data): array
    {
        $dataFormatted = [];

        $actualCurrencies = config('currencies.actual');

        foreach ($data as $k => $item) {

            if (!in_array($k, $actualCurrencies)) {
                continue;
            }

            $dataFormatted[] = [
                'slug' => strtoupper($k),
                'value' => floatval($item),
            ];
        }

        return $dataFormatted ?? [];
    }

    private function curl($params)
    {
        $url = $params['url'] ?? '';
        $method = $params['method'] ?? '';

        $httpQuery = $params['httpQuery'] ?? [];
        $httpQuery = !empty($httpQuery) ? "?" . http_build_query($httpQuery) : "";

        $options = [
            CURLOPT_URL => "{$url}{$method}{$httpQuery}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        $errorNumber = curl_errno($ch);
        $errorMessage = curl_error($ch);

        curl_close($ch);

        if (!empty($errorNumber)) {
            $errorMessage = !empty($errorMessage) ? $errorMessage : 'Ошибка CURL';
            return [
                'error' => 'curlError',
                'message' => "{$errorNumber} - {$errorMessage}"
            ];
        }

        $response = json_decode($response ?? '', true);

        if (empty($response)) {
            return ['error' => 1, 'message' => 'Ошибка при парсинге ответа с сервера'];
        }

        return $response;
    }

}
