<?php

namespace App\Services\Handlers\Commands;

use App\Models\User;

class StatusCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ?User $user)
    {
    }

    public function handle(): ?string
    {
        return $this->user->status->handler();
    }
}
