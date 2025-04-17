<?php

namespace App\Services\Handlers\Updates;

use App\Http\Controllers\MainMenu;
use App\Services\Handlers\FindCommandHandler;
use App\Utils\Update as CustomUpdate;

class TextOrCallbackQueryHandler implements UpdateHandlerInterface
{
    public function supports(CustomUpdate $update): bool
    {
        return $update->getMessage()?->getText() !== null || $update->getCallbackQuery() !== null;
    }

    public function handle(CustomUpdate $update): void
    {
        $handlerClass = $this->determineHandler($update);

        app()->make($handlerClass, ['update' => $update])->handle();
    }

    private function determineHandler(CustomUpdate $update): string
    {
        $handlerClass = $update->getCallbackQuery()
            ? $update->getCallbackAction()?->handler()
            : (new FindCommandHandler($update))->findCommandHandler();

        return $handlerClass && class_exists($handlerClass)
            ? $handlerClass
            : MainMenu::class;
    }
}
