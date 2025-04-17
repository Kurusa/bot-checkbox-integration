<?php

namespace App\Services;

use App\Enums\CallbackAction\BackAction;
use App\Enums\CallbackAction\CallbackAction;

class CallbackActionRegistry
{
    public static function getEnums(): array
    {
        return [
            CallbackAction::class,
            BackAction::class,
        ];
    }
}
