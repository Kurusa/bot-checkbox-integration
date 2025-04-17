<?php

namespace App\Services\Handlers\Commands;

interface CommandHandlerInterface
{
    public function handle(): ?string;
}
