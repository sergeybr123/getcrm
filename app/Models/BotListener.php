<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotListener extends Model
{
    protected $table = 'bot_listeners';

    protected $fillable = [
        'bot_id',
        'text',
        'bot_listener_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
