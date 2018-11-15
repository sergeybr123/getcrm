<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingSubscribe extends Model
{
    protected $connection = 'billing';

    protected $fillable = [
        'user_id',
        'plan_id',
        'interval',
        'trial_ends_at',
        'start_at',
        'end_at',
        'active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
