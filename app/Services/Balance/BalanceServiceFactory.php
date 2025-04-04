<?php

namespace App\Services\Balance;

use InvalidArgumentException;

class BalanceServiceFactory
{
    public function make(string $type): BalancerServiceInterface
    {
        return match (strtolower($type)) {
            'checkbox' => app(CheckboxBalanceService::class),
            'mono' => app(MonoBalanceService::class),
            'privat' => app(PrivatBalanceService::class),
            //'novapay' => app(NovaPayBalanceService::class),
            default => throw new InvalidArgumentException("Unsupported service type: {$type}"),
        };
    }
}
