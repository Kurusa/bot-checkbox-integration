<?php

namespace App\Console\Commands;

use App\Jobs\FetchApiKeysJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class UpdateFinancialDataCommand extends Command
{
    protected $signature = 'update-financial-data';

    protected $description = 'Fetch financial data using API keys and update Google Sheets';

    public function handle(): int
    {
        Bus::dispatchNow(new FetchApiKeysJob());

        return Command::SUCCESS;
    }
}
