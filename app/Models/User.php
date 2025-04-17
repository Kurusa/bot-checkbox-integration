<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $chat_id telegram chat ID of the user. Negative for group chats.
 * @property UserStatus $status
 */
class User extends Model
{
    protected $fillable = [
        'chat_id',
        'status',
    ];

    protected $casts = [
        'status' => UserStatus::class,
    ];
}
