<?php

namespace App\Enums\CallbackAction;

interface CallbackActionEnum
{
    public function handler(): string;
}
