<?php

namespace App\Services\Handlers\Commands;

use function array_flip;

class KeyboardCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ?string $text,
        private readonly array   $keyboardHandlers,
    )
    {
    }

    public function handle(): ?string
    {
        $key = $this->processKeyboardCommand($this->text);

        if ($key) {
            if (isset($this->keyboardHandlers[$key])) {
                return $this->keyboardHandlers[$key];
            }
        }

        return null;
    }

    private function processKeyboardCommand(?string $text): ?string
    {
        $translations = array_flip(__('texts'));

        if (isset($translations[$text])) {
            return $translations[$text];
        }

        return null;
    }
}
