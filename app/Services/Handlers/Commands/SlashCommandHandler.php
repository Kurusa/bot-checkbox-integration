<?php

namespace App\Services\Handlers\Commands;

class SlashCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ?string $text,
        private readonly array   $slashHandlers,
    )
    {
    }

    public function handle(): ?string
    {
        if (str_starts_with($this->text, '/')) {
            if (isset($this->slashHandlers[$this->text])) {
                return $this->slashHandlers[$this->text];
            }
        }

        return null;
    }
}
