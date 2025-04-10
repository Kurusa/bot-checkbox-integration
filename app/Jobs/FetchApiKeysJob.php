<?php

namespace App\Jobs;

use App\Services\GoogleSheetService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchApiKeysJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private const API_KEYS_RANGE = 'API_checkbox!C2:E30';

    public function handle(GoogleSheetService $googleSheetService): void
    {
        $data = $googleSheetService->getSpreadsheetValues(
            env('BASE_SPREADSHEET_ID'),
            self::API_KEYS_RANGE,
        );

        foreach ($data->getValues() as $index => $row) {
            $apiKey = $row[0] ?? null;
            $type = ($row[2] ?? 'checkbox');

            if ($apiKey) {
                dispatch(new ProcessApiKeyJob(
                    $apiKey,
                    $index + 1,
                    $type,
                ));
            }
        }
    }
}
