<?php

namespace App\Jobs;

use App\Services\GoogleSheetService;
use Carbon\Carbon;
use Google\Service\Sheets\ValueRange;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateGoogleSheetJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int    $totalTurnover,
        private readonly int    $apiKeyIndex,
        private readonly string $type,
    )
    {
    }

    public function handle(GoogleSheetService $googleSheetService): void
    {
        $spreadsheetId = env('BASE_SPREADSHEET_ID');

        if ($this->type === 'checkbox') {
            $data = $googleSheetService->getSpreadsheetValues($spreadsheetId, $this->apiKeyIndex);

            $rowIndex = count($data->getValues()) + 1;

            $body = new ValueRange([
                'values' => [[
                    Carbon::yesterday()->format('m/d/Y'),
                    $this->totalTurnover,
                ]]
            ]);

            $googleSheetService->writeSpreadsheetValue(
                $spreadsheetId,
                $body,
                "$this->apiKeyIndex!A{$rowIndex}:B{$rowIndex}"
            );
        } else {
            $googleSheetService->writeSpreadsheetValue(
                $spreadsheetId,
                new ValueRange([
                    'values' => [[
                        $this->totalTurnover,
                    ]]
                ]),
                'D' . ($this->apiKeyIndex + 1),
            );
        }
    }
}
