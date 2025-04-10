<?php

namespace App\Jobs;

use App\Services\Balance\BalanceServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessApiKeyJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $apiKey,
        private readonly int    $apiKeyIndex,
        private readonly string $type,
    )
    {
    }

    public function handle(BalanceServiceFactory $factory): void
    {
        $service = $factory->make($this->type);
        $totalTurnover = $service->getTotalTurnover($this->apiKey);

        dispatch(new UpdateGoogleSheetJob($totalTurnover, $this->apiKeyIndex, $this->type));
    }
}
