<?php

namespace App\Console\Commands;

use App\Jobs\FetchApiKeysJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class UpdateFinancialDataCommand extends Command
{
    protected $signature = 'update-financial-data {source? : mono|privat|checkbox|novapay}';

    protected $description = 'Fetch financial data using API keys and update Google Sheets';

    public function handle(): int
    {
        $source = $this->argument('source');

        if ($source && !in_array($source, ['mono', 'privat', 'checkbox', 'novapay'])) {
            $this->error('Invalid source type. Use: mono, privat, checkbox, novapay.');
            return Command::INVALID;
        }

        Bus::dispatchNow(new FetchApiKeysJob($source));

        return Command::SUCCESS;
    }
}
