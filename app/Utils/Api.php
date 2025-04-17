<?php

namespace App\Utils;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Api extends BotApi
{
    private int $chatId;

    public function __construct(
        protected $token,
        ?string   $trackerToken = null
    )
    {
        parent::__construct($token, $trackerToken);
    }

    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function sendMessageWithKeyboard(
        string                                   $text,
        InlineKeyboardMarkup|ReplyKeyboardMarkup $keyboard,
        int                                      $messageIdToUpdate = null,
    ): Message
    {
        try {
            if ($messageIdToUpdate) {
                return $this->editMessageWithInlineKeyboard(
                    $messageIdToUpdate,
                    $text,
                    $keyboard,
                );
            }

            return parent::sendMessage(
                $this->chatId,
                $text,
                'markdown',
                true,
                null,
                $keyboard,
            );
        } catch (Exception $e) {
            return $this->notifyAdmin($e->getMessage());
        }
    }

    public function sendText(
        string $text,
        bool   $disableNotification = false,
    ): Message
    {
        try {
            return parent::sendMessage(
                $this->chatId,
                $text,
                'markdown',
                false,
                null,
                null,
                $disableNotification,
            );
        } catch (Exception $e) {
            return $this->notifyAdmin($e->getMessage());
        }
    }

    public function notifyAdmin(string $exception): Message
    {
        return parent::sendMessage(
            config('telegram.admin_chat_id'),
            $exception,
        );
    }

    private function editMessageWithInlineKeyboard(
        $messageId,
        string $text,
        $keyboard,
    ): Message
    {
        try {
            return parent::editMessageText(
                $this->chatId,
                $messageId,
                $text,
                'markdown',
                false,
                $keyboard,
            );
        } catch (Exception $e) {
            return $this->notifyAdmin($e->getMessage());
        }
    }

    public function deleteMessageById(int $messageId): Message|bool
    {
        try {
            return parent::deleteMessage(
                $this->chatId,
                $messageId,
            );
        } catch (Exception $e) {
            return $this->notifyAdmin($e->getMessage());
        }
    }
}
