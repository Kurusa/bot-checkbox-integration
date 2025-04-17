<?php

namespace App\Enums\CallbackAction;

use App\Http\Controllers\HandleAccountSelect;

enum CallbackAction: int implements CallbackActionEnum
{
    case ACCOUNT_SELECT = 1;

    public function handler(): string
    {
        return match ($this) {
            self::ACCOUNT_SELECT => HandleAccountSelect::class,
        };
    }
}
