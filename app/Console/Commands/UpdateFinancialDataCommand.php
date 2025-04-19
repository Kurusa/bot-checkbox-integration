<?php

namespace App\Console\Commands;

use App\Jobs\FetchApiKeysJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

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

        Log::info('Fetching data for ' . $source. '. Time: ' . Carbon::now()->format('Y-m-d H:i:s'));

        Bus::dispatchNow(new FetchApiKeysJob($source));

        return Command::SUCCESS;
    }
}
