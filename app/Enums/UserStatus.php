<?php

namespace App\Enums;

use App\Http\Controllers\HandleOTPPassword;
use App\Http\Controllers\MainMenu;

enum UserStatus: string
{
    case MAIN_MENU = 'main_menu';
    case WAITING_FOR_OTP_PASSWORD = 'waiting_for_otp_password';

    public function handler(): string
    {
        return match ($this) {
            self::MAIN_MENU => MainMenu::class,
            self::WAITING_FOR_OTP_PASSWORD => HandleOTPPassword::class,
        };
    }
}
