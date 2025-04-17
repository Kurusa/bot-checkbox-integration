<?php

namespace App\Services\Handlers\Updates;

use App\Utils\Update as CustomUpdate;

interface UpdateHandlerInterface
{
    public function supports(CustomUpdate $update): bool;

    public function handle(CustomUpdate $update): void;
}
