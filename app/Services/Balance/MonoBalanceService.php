<?php

namespace App\Services\Balance;

use Carbon\Carbon;
use GuzzleHttp\Client;

class MonoBalanceService implements BalancerServiceInterface
{
    private const ACCOUNTS_URL = 'https://api.monobank.ua/personal/client-info';
    private const STATEMENT_URL = 'https://api.monobank.ua/personal/statement/{account}/{from}/{to}';

    public function getTotalTurnover(string $apiKey): int
    {
        $client = new Client();

        $accountsResponse = $client->get(self::ACCOUNTS_URL, [
            'headers' => [
                'X-Token' => $apiKey,
            ],
        ]);

        $accounts = json_decode($accountsResponse->getBody()->getContents(), true)['accounts'] ?? [];

        $fopAccounts = collect($accounts)->filter(fn($acc) => $acc['type'] === 'fop')->pluck('id');

        $from = Carbon::yesterday()->startOfDay()->timestamp;
        $to = Carbon::yesterday()->endOfDay()->timestamp;

        $totalIncome = 0;

        foreach ($fopAccounts as $accountId) {
            $totalIncome += $this->fetchAccountIncome($client, $apiKey, $accountId, $from, $to);
        }

        return $totalIncome;
    }

    private function fetchAccountIncome(Client $client, string $apiKey, string $accountId, int $from, int $to): int
    {
        $totalIncome = 0;
        $currentTo = $to;

        do {
            $url = str_replace(['{account}', '{from}', '{to}'], [$accountId, $from, $currentTo], self::STATEMENT_URL);

            $response = $client->get($url, [
                'headers' => [
                    'X-Token' => $apiKey,
                ],
            ]);

            $transactions = json_decode($response->getBody()->getContents(), true);

            foreach ($transactions as $tx) {
                if ($tx['amount'] > 0) {
                    $totalIncome += $tx['amount'];
                }
            }

            $hasMore = count($transactions) === 500;

            if ($hasMore) {
                $lastTransaction = end($transactions);
                $currentTo = $lastTransaction['time'] - 1;
            }
        } while ($hasMore);

        return $totalIncome;
    }
}
