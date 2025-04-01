<?php

namespace App\Jobs;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessApiKeyJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $apiKey,
        private readonly int    $apiKeyIndex,
    )
    {
    }

    public function handle(): void
    {
        $client = new Client();

        try {
            $response = $client->get('https://api.checkbox.ua/api/v1/reports/periodical', [
                'headers' => [
                    'accept' => 'text/plain',
                    'X-License-Key' => $this->apiKey,
                ],
                'query' => [
                    'is_short' => 'true',
                    'from_date' => Carbon::yesterday()->startOfDay()->toISOString(),
                    'to_date' => Carbon::yesterday()->endOfDay()->toISOString(),
                ],
            ]);

            $totalTurnover = $this->parseTotalTurnover($response->getBody()->getContents());

            dispatch(new UpdateGoogleSheetJob($totalTurnover, $this->apiKeyIndex));
        } catch (Exception $e) {
        }
    }

    private function parseTotalTurnover(string $content): int
    {
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (str_contains($line, 'Загальний оборот')) {
                $parts = explode(' ', trim($line));
                return (int) end($parts);
            }
        }

        return 0;
    }
}
