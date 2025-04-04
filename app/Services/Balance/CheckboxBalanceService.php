<?php

namespace App\Services\Balance;

use Carbon\Carbon;
use GuzzleHttp\Client;

class CheckboxBalanceService implements BalancerServiceInterface
{
    public function getTotalTurnover(string $apiKey): int
    {
        $client = new Client();

        $response = $client->get('https://api.checkbox.ua/api/v1/reports/periodical', [
            'headers' => [
                'accept' => 'text/plain',
                'X-License-Key' => $apiKey,
            ],
            'query' => [
                'is_short' => 'true',
                'from_date' => Carbon::yesterday()->startOfDay()->toISOString(),
                'to_date' => Carbon::yesterday()->endOfDay()->toISOString(),
            ],
        ]);

        return $this->parseTotalTurnover($response->getBody()->getContents());
    }

    private function parseTotalTurnover(string $content): int
    {
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (str_contains($line, 'Загальний оборот')) {
                $parts = explode(' ', trim($line));
                return (int)end($parts);
            }
        }

        return 0;
    }
}
