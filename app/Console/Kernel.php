<?php

namespace App\Console;

use App\Console\Commands\RefreshNovaPayPrincipals;
use App\Console\Commands\UpdateFinancialDataCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        UpdateFinancialDataCommand::class,
        RefreshNovaPayPrincipals::class,
    ];

    protected function schedule(Schedule $schedule)
    {
    }
}
