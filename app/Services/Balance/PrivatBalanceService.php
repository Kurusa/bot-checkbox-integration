<?php

namespace App\Services\Balance;

use GuzzleHttp\Client;

class PrivatBalanceService implements BalancerServiceInterface
{
    private const BALANCE_URL = 'https://acp.privatbank.ua/api/statements/balance/final';

    public function getTotalTurnover(string $apiKey): int
    {
        $client = new Client();

        $response = $client->get(self::BALANCE_URL, [
            'headers' => [
                'Accept' => 'application/json',
                'token' => $apiKey,
                'Content-Type' => 'application/json;charset=cp1251',
            ],
        ]);

        $rawResponse = $response->getBody()->getContents();
        $utf8Response = mb_convert_encoding($rawResponse, 'UTF-8', 'CP1251');
        $data = json_decode($utf8Response, true);

        $balances = $data['balances'] ?? [];

        $totalBalance = 0;

        foreach ($balances as $balance) {
            $totalBalance += floatval($balance['balanceOutEq'] ?? 0);
        }

        return (int)round($totalBalance);
    }
}
