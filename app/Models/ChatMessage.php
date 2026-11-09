<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    /** @use HasFactory<ChatMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'order_code',
        'sender_type',
        'sender_name',
        'sender_avatar',
        'message',
        'message_type',
        'metadata',
        'is_read',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
    ];
}
