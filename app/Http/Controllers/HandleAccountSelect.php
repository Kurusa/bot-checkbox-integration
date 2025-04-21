<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Models\NovaPayAccount;
use App\Services\Balance\NovaPayBalanceService;
use Exception;

class HandleAccountSelect extends BaseCommand
{
    public function handle(): void
    {
        $accountId = $this->update->getCallbackQueryByKey('id');

        /** @var NovaPayAccount|null $account */
        $account = NovaPayAccount::find($accountId);

        if (!$account) {
            $this->getBot()->sendText('Акаунт не знайдено або відсутні дані.');
            return;
        }

        /** @var NovaPayBalanceService $balanceService */
        $balanceService = app(NovaPayBalanceService::class);

        if (!$account->principal) {
            $this->beginAuthFlow($balanceService, $account);
            return;
        }

        try {
            $balance = $balanceService->getAvailableBalance($account->principal);
            $this->getBot()->sendText("Баланс акаунту \"{$account->account_name}\": {$balance} грн.");
        } catch (Exception $e) {
            $this->beginAuthFlow($balanceService, $account);
            return;
        }
    }

    private function beginAuthFlow(NovaPayBalanceService $balanceService, NovaPayAccount $account): void
    {
        $preAuthData = $balanceService->preAuthenticate("{$account->login}:{$account->password}");

        $account->update([
            'principal' => null,
            'principal_valid_until' => null,
            'temp_principal' => $preAuthData['temp_principal'],
            'code_operation_otp' => $preAuthData['code_operation_otp'],
        ]);

        if (empty($preAuthData['temp_principal']) || empty($preAuthData['code_operation_otp'])) {
            $this->getBot()->sendText('Авторизацію пройти неможливо, оновіть логін і пароль.');
            return;
        }

        $this->user->update([
            'status' => UserStatus::WAITING_FOR_OTP_PASSWORD,
        ]);

        $this->getBot()->sendText('Сесію втрачено або токен недійсний. Вам надіслано SMS з кодом. Введіть OTP для повторної авторизації.');
    }
}
