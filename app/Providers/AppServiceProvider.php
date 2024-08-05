<?php

namespace App\Providers;

use App\Services\GoogleSheetService;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

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

            return new GoogleSheetService($service, $client);
        });
    }
}
