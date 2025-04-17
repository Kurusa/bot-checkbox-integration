<?php

namespace App\Http\Controllers;

use App\Enums\CallbackAction\CallbackAction;
use App\Models\NovaPayAccount;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class MainMenu extends BaseCommand
{
    public function handle(): void
    {
        $accounts = NovaPayAccount::all();

        $buttons = $accounts->map(fn(NovaPayAccount $account) => [
            [
                'text' => ($account->principal ? '✅ ' : '❌ ') . $account->account_name,
                'callback_data' => json_encode([
                    'id' => $account->id,
                    'a' => CallbackAction::ACCOUNT_SELECT->value,
                ]),
            ],
        ])->toArray();

        $this->getBot()->sendMessageWithKeyboard(
            'Тут ви можете налаштувати інтеграцію з novapay. Доступні акаунти:',
            new InlineKeyboardMarkup($buttons),
        );
    }
}
