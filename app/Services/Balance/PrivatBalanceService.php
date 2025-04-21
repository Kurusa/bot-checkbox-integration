<?php

namespace App\Services\Balance;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class PrivatBalanceService implements BalancerServiceInterface
{
    private const BALANCE_URL = 'https://acp.privatbank.ua/api/statements/balance';

    private string $accountNumber = '';

    public function getTotalTurnover(string $apiKey): int
    {
        $client = new Client();

        $response = $client->get(self::BALANCE_URL . '?startDate=' . Carbon::now()->format('d-m-Y'), [
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

        foreach ($balances as $balance) {
            if ($balance['acc'] === $this->accountNumber) {
                return (int)round(floatval($balance['balanceOutEq'] ?? 0));
            }
        }

        return 0;
    }

    public function setAccountNumber(string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }
}
