<?php

namespace App\Services\Handlers\Commands;

class RegExpCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ?string $text,
        private readonly array   $regExpHandlers,
    )
    {
    }

    public function handle(): ?string
    {
        foreach ($this->regExpHandlers as $pattern => $handler) {
            if (preg_match($pattern, $this->text)) {
                return $handler;
            }
        }

        return null;
    }
}
