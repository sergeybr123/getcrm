<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'type',
        'data',
        'account_service_id'
    ];
    protected $casts = [
        'data' => 'array',
    ];
}
