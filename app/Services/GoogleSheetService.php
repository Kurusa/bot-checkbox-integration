<?php

namespace App\Services;

use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    public function __construct(private readonly Sheets $sheetsService)
    {
    }

    public function getSpreadsheetValues(string $spreadsheetId, ?string $range): ValueRange
    {
        return $this->sheetsService->spreadsheets_values->get($spreadsheetId, $range);
    }

    public function appendSpreadsheetValue(
        string     $spreadsheetId,
        ValueRange $body,
        string     $range,
    ): void
    {
        $this->sheetsService->spreadsheets_values->append(
            $spreadsheetId,
            $range,
            $body,
            [
                'valueInputOption' => 'USER_ENTERED',
            ]
        );
    }

    public function updateSpreadsheetValue(
        string     $spreadsheetId,
        ValueRange $body,
        string     $range,
    ): void
    {
        $this->sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $body,
            [
                'valueInputOption' => 'USER_ENTERED',
            ]
        );
    }
}
