<?php

namespace App\Jobs;

use App\Services\Balance\BalanceRequest;
use App\Services\GoogleSheetService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchApiKeysJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private const API_KEYS_RANGE = 'API_checkbox!C2:F30';

    public function __construct(private readonly ?string $filterType)
    {
    }

    public function handle(GoogleSheetService $googleSheetService): void
    {
        $data = $googleSheetService->getSpreadsheetValues(
            env('BASE_SPREADSHEET_ID'),
            self::API_KEYS_RANGE,
        );

        foreach ($data->getValues() as $index => $row) {
            $apiKey = $row[0] ?? null;
            $type = $row[2] ?? 'checkbox';
            $accountNumber = $row[3] ?? null;

            if ($apiKey && (!$this->filterType || strtolower($type) === strtolower($this->filterType))) {
                dispatch(new ProcessApiKeyJob(new BalanceRequest(
                    apiKey: $apiKey,
                    type: $type,
                    accountNumber: $accountNumber,
                    apiKeyIndex: $index + 1,
                )));
            }
        }
    }
}
