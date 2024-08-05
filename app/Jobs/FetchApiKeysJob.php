<?php

namespace App\Jobs;

use App\Services\GoogleSheetService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchApiKeysJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GoogleSheetService $googleSheetService): void
    {
        $spreadsheetId = env('BASE_SPREADSHEET_ID');

        $range = 'API_checkbox!C2:C30';

        $data = $googleSheetService->getSpreadsheetValues($spreadsheetId, $range);

        foreach ($data->getValues() as $index => $apiKey) {
            dispatch(new ProcessApiKeyJob($apiKey[0], $index + 1));
        }
    }
}
