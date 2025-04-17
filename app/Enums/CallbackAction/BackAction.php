<?php

namespace App\Enums\CallbackAction;

enum BackAction: int implements CallbackActionEnum
{
    case BACK_TEST = 2;

    public function handler(): string
    {
        return match ($this) {
            self::BACK_TEST => '',
        };
    }
}
