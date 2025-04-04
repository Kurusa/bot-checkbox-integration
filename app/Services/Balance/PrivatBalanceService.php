<?php

namespace App\Services\Balance;

use Carbon\Carbon;
use GuzzleHttp\Client;

class PrivatBalanceService implements BalancerServiceInterface
{
    private const STATEMENT_URL = 'https://acp.privatbank.ua/api/statements/transactions';

    public function getTotalTurnover(string $apiKey): int
    {
        $client = new Client();

        $fromDate = Carbon::yesterday()->format('d-m-Y');
        $toDate = Carbon::yesterday()->format('d-m-Y');

        $response = $client->get(self::STATEMENT_URL, [
            'headers' => [
                'Accept' => 'application/json',
                'token' => $apiKey,
                'Content-Type' => 'application/json;charset=cp1251',
            ],
            'query' => [
                'startDate' => $fromDate,
                'endDate' => $toDate,
                'limit' => 400,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_INVALID_UTF8_IGNORE);

        $transactions = $data['transactions'] ?? [];

        $totalIncome = 0;

        foreach ($transactions as $transaction) {
            if (($transaction['TRANTYPE'] ?? null) === 'C') {
                $totalIncome += (int)round(floatval($transaction['SUM_E']));
            }
        }

        return $totalIncome;
    }
}
