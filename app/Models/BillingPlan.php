<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    protected $connection = 'billing';

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
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
