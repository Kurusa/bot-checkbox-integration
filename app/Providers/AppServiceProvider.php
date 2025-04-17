<?php

namespace App\Providers;

use App\Services\GoogleSheetService;
use App\Services\Handlers\UpdateProcessorService;
use App\Services\Handlers\Updates\TextOrCallbackQueryHandler;
use App\Services\NovaPayAccountsService;
use App\Utils\Api;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use TelegramBot\Api\Client as TelegramClient;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot(): void
    {
        $this->app->singleton(GoogleSheetService::class, function () {
            $client = new Client();
            $client->setAuthConfig(Storage::disk('public')->path('credentials.json'));
            $client->addScope([Sheets::SPREADSHEETS, Drive::DRIVE_FILE]);

            $service = new Sheets($client);

            return new GoogleSheetService($service);
        });

        $this->app->bind(UpdateProcessorService::class, function ($app) {
            return new UpdateProcessorService([
                $app->make(TextOrCallbackQueryHandler::class),
            ]);
        });

        $this->app->singleton(TelegramClient::class, function () {
            return new TelegramClient(config('telegram.telegram_bot_token'));
        });

        $this->app->singleton(Api::class, function () {
            return new Api(config('telegram.telegram_bot_token'));
        });
    }
}
