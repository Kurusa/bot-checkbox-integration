<?php

namespace App\Http\Controllers;

use App\Services\Handlers\UpdateProcessorService;
use Exception;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Client;

class WebhookController
{
    public function __construct(private readonly UpdateProcessorService $updateProcessorService)
    {
    }

    public function handle(Client $client): void
    {
        try {
            $client->on(
                fn($update) => $this->updateProcessorService->process($update),
                fn($update) => $this->updateProcessorService->shouldProcess($update)
            )->run();
        } catch (Exception $exception) {
            Log::error('WebhookController: ' . $exception->getMessage() . $exception->getTraceAsString());
        }
    }
}
