<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Models\NovaPayAccount;
use App\Services\Balance\NovaPayBalanceService;

class HandleOTPPassword extends BaseCommand
{
    public function handle(): void
    {
        $otpPassword = $this->update->getMessageText();

        /** @var NovaPayAccount|null $account */
        $account = NovaPayAccount::whereNotNull('temp_principal')
            ->whereNotNull('code_operation_otp')
            ->first();

        /** @var NovaPayBalanceService $balanceService */
        $balanceService = app(NovaPayBalanceService::class);

        $principal = $balanceService->authenticate(
            $account->temp_principal,
            $account->code_operation_otp,
            $otpPassword,
        );

        if (!$principal) {
            $this->getBot()->sendText('Пароль невірний або час сесії минув. Спробуйте ще раз.');
            $this->user->update([
                'status' => UserStatus::MAIN_MENU,
            ]);
            $account->update([
                'temp_principal' => null,
                'code_operation_otp' => null,
            ]);
            $this->triggerCommand(MainMenu::class);
            return;
        }

        $account->update([
            'principal' => $principal,
            'temp_principal' => null,
            'code_operation_otp' => null,
        ]);

        $this->user->update([
            'status' => UserStatus::MAIN_MENU,
        ]);

        $this->getBot()->sendText('Авторизація успішна.');
        $this->triggerCommand(MainMenu::class);
    }
}
