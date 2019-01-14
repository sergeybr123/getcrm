<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotSavingData extends Model
{
    protected $table = 'bot_saving_datas';

    protected $fillable = [
        'user_id',
        'company_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
