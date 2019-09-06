<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'code',
        'name',
        'discount',
        'description',
        'price',
        'interval',
        'trial_period_days',
        'sort_order',
        'on_show',
        'active',
        'bot_count',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
