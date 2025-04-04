<?php

namespace App\Services\Balance;

interface BalancerServiceInterface
{
    public function getTotalTurnover(string $apiKey): int;
}
