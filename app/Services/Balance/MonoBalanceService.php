<?php

namespace App\Services\Balance;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class MonoBalanceService implements BalancerServiceInterface
{
    private const ACCOUNTS_URL = 'https://api.monobank.ua/personal/client-info';

    public function getTotalTurnover(string $apiKey): int
    {
        $client = new Client();

        $accountsResponse = $client->get(self::ACCOUNTS_URL, [
            'headers' => [
                'X-Token' => $apiKey,
            ],
        ]);

        $accounts = json_decode($accountsResponse->getBody()->getContents(), true)['accounts'] ?? [];

        $fopAccounts = $this->getFopAccounts($accounts);

        $totalBalance = $fopAccounts->sum('balance');

        return (int) ($totalBalance / 100);
    }

    private function getFopAccounts(array $accounts): Collection
    {
        return collect($accounts)->filter(fn($acc) => $acc['type'] === 'fop');
    }
}
