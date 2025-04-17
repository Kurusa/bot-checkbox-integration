<?php

namespace App\Services\Handlers;

use App\Services\Handlers\Updates\UpdateHandlerInterface;
use App\Utils\Update as CustomUpdate;
use TelegramBot\Api\Types\Update;

class UpdateProcessorService
{
    public function __construct(private readonly iterable $handlers)
    {
    }

    public function process(Update $update): void
    {
        $customUpdate = new CustomUpdate($update);

        /** @var UpdateHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->supports($customUpdate)) {
                $handler->handle($customUpdate);
                return;
            }
        }
    }

    public function shouldProcess(Update $update): bool
    {
        /** @var UpdateHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->supports(new CustomUpdate($update))) {
                return true;
            }
        }

        return false;
    }
}
