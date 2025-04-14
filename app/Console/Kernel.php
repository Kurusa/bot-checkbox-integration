<?php

namespace App\Console;

use App\Console\Commands\UpdateFinancialDataCommand;
use App\Console\Commands\UpdateNovapayPrincipalCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        UpdateFinancialDataCommand::class,
        UpdateNovapayPrincipalCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
    }
}
