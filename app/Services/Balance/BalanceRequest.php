<?php

namespace App\Services\Balance;

class BalanceRequest
{
    public function __construct(
        public readonly string  $apiKey,
        public readonly string  $type,
        public readonly ?string $accountNumber = null,
        public readonly ?int    $apiKeyIndex = null,
    )
    {
    }
}
