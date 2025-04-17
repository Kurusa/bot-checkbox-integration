<?php

namespace App\Services\Handlers;

use App\Models\User;
use App\Services\Handlers\Commands\KeyboardCommandHandler;
use App\Services\Handlers\Commands\RegExpCommandHandler;
use App\Services\Handlers\Commands\SlashCommandHandler;
use App\Services\Handlers\Commands\StatusCommandHandler;
use App\Utils\Update;

class FindCommandHandler
{
    private ?string $text;
    private array $handlers;
    private User $user;

    public function __construct(Update $update)
    {
        $this->user = request()->get('user');
        $this->text = $update->getMessageText();
        $this->handlers = config('telegram.handlers');
    }

    public function findCommandHandler(): string|array|null
    {
        return (new SlashCommandHandler($this->text, $this->handlers['slash']))->handle() ??
            (new KeyboardCommandHandler($this->text, $this->handlers['keyboard']))->handle() ??
            (new StatusCommandHandler($this->user))->handle() ??
            (new RegExpCommandHandler($this->text, $this->handlers['reg_exp']))->handle();
    }
}
