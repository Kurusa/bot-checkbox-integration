<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Models\User;
use App\Utils\Api;
use Closure;
use Illuminate\Http\Request;

class LoadTelegramUser
{
    public function handle(Request $request, Closure $next)
    {
        $rawUpdate = json_decode($request->getContent(), true);

        $chatId = $this->extractChatId($rawUpdate);

        if (!$chatId) {
            abort(400, 'Unable to extract Telegram user or chat ID');
        }

        $user = $this->findOrCreateUser($chatId);

        $this->setupApi($chatId);

        $request->merge(['user' => $user]);

        return $next($request);
    }

    private function extractChatId(array $rawUpdate): ?int
    {
        return $rawUpdate['callback_query']['message']['chat']['id']
            ?? $rawUpdate['callback_query']['from']['id']
            ?? $rawUpdate['message']['chat']['id']
            ?? $rawUpdate['message']['from']['id']
            ?? $rawUpdate['inline_query']['from']['id']
            ?? null;
    }

    private function findOrCreateUser(int $chatId): User
    {
        return User::firstOrCreate(
            ['chat_id' => $chatId],
            [
                'status' => UserStatus::MAIN_MENU,
            ]
        );
    }

    private function setupApi(int $chatId): void
    {
        /** @var Api $api */
        $api = app(Api::class);
        $api->setChatId($chatId);
    }
}
