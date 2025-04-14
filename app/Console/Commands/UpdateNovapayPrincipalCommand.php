<?php

namespace App\Console\Commands;

use App\Services\Balance\NovaPayBalanceService;
use App\Services\GoogleSheetService;
use Illuminate\Console\Command;

class UpdateNovapayPrincipalCommand extends Command
{
    protected $signature = 'update-novapay-principal';

    private const API_KEYS_RANGE = 'API_checkbox!C2:E30';

    public function handle(
        GoogleSheetService    $googleSheetService,
        NovaPayBalanceService $novaPayBalanceService,
    ): void
    {
        $data = $googleSheetService->getSpreadsheetValues(
            env('BASE_SPREADSHEET_ID'),
            self::API_KEYS_RANGE,
        );

        foreach ($data->getValues() as $row) {
            $apiKey = $row[0] ?? null;
            $type = ($row[2] ?? 'checkbox');

            if ($apiKey && ($type === 'novapay')) {
                $novaPayBalanceService->refreshPrincipal($apiKey);
            }
        }
    }
}
