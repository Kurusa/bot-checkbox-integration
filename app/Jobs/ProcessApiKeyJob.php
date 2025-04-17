<?php

namespace App\Jobs;

use App\Services\Balance\BalanceRequest;
use App\Services\Balance\BalanceServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessApiKeyJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly BalanceRequest $request)
    {
    }

    public function handle(BalanceServiceFactory $factory): void
    {
        $service = $factory->make($this->request->type);

        if (!empty($this->request->accountNumber)) {
            $service->setAccountNumber($this->request->accountNumber);
        }

        $totalTurnover = $service->getTotalTurnover($this->request->apiKey);

        dispatch(new UpdateGoogleSheetJob(
            $totalTurnover,
            $this->request->apiKeyIndex,
            $this->request->type
        ));
    }
}
