<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotAnswer extends Model
{
    protected $table = 'bot_answers';

    protected $fillable = [
        'bot_answer_id',
        'order',
        'bot_listener_id',
        'type',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
