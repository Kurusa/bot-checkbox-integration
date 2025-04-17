<?php

namespace App\Utils;

use App\Enums\CallbackAction\CallbackActionEnum;
use App\Services\CallbackActionRegistry;
use TelegramBot\Api\Types\Update as BaseUpdate;

class Update extends BaseUpdate
{
    private array $decodedCallbackQueryData = [];

    public function __construct(BaseUpdate $update)
    {
        if ($update->getCallbackQuery()) {
            parent::setCallbackQuery($update->getCallbackQuery());
        }

        if ($update->getMessage()) {
            parent::setMessage($update->getMessage());
        }

        if ($update->getInlineQuery()) {
            parent::setInlineQuery($update->getInlineQuery());
        }

        if ($update->getChosenInlineResult()) {
            parent::setChosenInlineResult($update->getChosenInlineResult());
        }
    }

    public function getDecodedCallbackQueryData(): array
    {
        if ($this->getCallbackQuery() && !$this->decodedCallbackQueryData) {
            $this->decodedCallbackQueryData = json_decode($this->getCallbackQuery()->getData(), true);
        }

        return $this->decodedCallbackQueryData;
    }

    public function getCallbackQueryByKey(string $key, $defaultValue = ''): mixed
    {
        return $this->getDecodedCallbackQueryData()[$key] ?? $defaultValue;
    }

    public function getCallbackAction(): ?CallbackActionEnum
    {
        $callbackActionValue = (int)$this->getCallbackQueryByKey('a');

        /** @var class-string<CallbackActionEnum> $enumClass */
        foreach (CallbackActionRegistry::getEnums() as $enumClass) {
            if ($enumInstance = $enumClass::tryFrom($callbackActionValue)) {
                return $enumInstance;
            }
        }

        return null;
    }

    public function getMessageText(): ?string
    {
        if ($this->getMessage()) {
            return $this->sanitizeCommand($this->getMessage()->getText());
        }

        if ($this->getCallbackQuery()) {
            return $this->getCallbackQuery()->getMessage()->getText();
        }

        return '';
    }

    public function getCallbackQueryMessageId(): int|null
    {
        return $this->getCallbackQuery()?->getMessage()?->getMessageId();
    }

    private function sanitizeCommand(?string $text): ?string
    {
        return str_replace('@synoptic_bot', '', $text);
    }
}
