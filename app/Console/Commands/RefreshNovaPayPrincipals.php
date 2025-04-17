<?php

namespace App\Console\Commands;

use App\Models\NovaPayAccount;
use App\Services\Balance\NovaPayBalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshNovaPayPrincipals extends Command
{
    protected $signature = 'novapay:refresh-principals';

    protected $description = 'Оновлює principal через NovaPay API для всіх акаунтів, які мають його збереженим';

    public function handle(): int
    {
        /** @var NovaPayBalanceService $balanceService */
        $balanceService = app(NovaPayBalanceService::class);

        $accounts = NovaPayAccount::whereNotNull('principal')->get();

        /** @var NovaPayAccount $account */
        foreach ($accounts as $account) {
            try {
                $refreshResult = $balanceService->refreshPrincipal($account->principal);
                $account->update([
                    'principal' => $refreshResult['principal'],
                    'principal_valid_until' => $refreshResult['principal_valid_until'],
                ]);

                $this->info("Оновлено principal для: {$account->account_name}");
            } catch (Throwable $e) {
                Log::info("Помилка для {$account->account_name}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}
