<?php

namespace App\Console\Commands;

use App\Jobs\FetchApiKeysJob;
use Illuminate\Console\Command;

class UpdateFinancialDataCommand extends Command
{
    protected $signature = 'update-financial-data';

    protected $description = 'Fetch financial data using API keys and update Google Sheets';

    public function handle(): int
    {
        dispatch(new FetchApiKeysJob());

        return Command::SUCCESS;
    }
}
