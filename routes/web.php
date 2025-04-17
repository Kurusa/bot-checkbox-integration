<?php

use Illuminate\Support\Facades\Cache;

$router->get('/', function () use ($router) {
    Cache::flush();

    return $router->app->version();
});

$router->post('/' . config('telegram.telegram_bot_token') . '/webhook', [
    'uses' => 'WebhookController@handle',
    'middleware' => 'load_telegram_user',
]);
