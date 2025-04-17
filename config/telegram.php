<?php

use App\Http\Controllers\MainMenu;

return [
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'admin_chat_id' => env('ADMIN_CHAT_ID'),

    'handlers' => [
        'keyboard' => [
        ],

        'slash' => [
            '/start' => MainMenu::class,
        ],
    ],
];
