<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotInput extends Model
{
    protected $table = 'bot_inputs';

    protected $fillable = [
        'bot_id',
        'data',
        'type',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
