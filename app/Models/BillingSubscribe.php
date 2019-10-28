<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BillingPlan;

class BillingSubscribe extends Model
{
    protected $table = 'subscribes';

    protected $fillable = [
        'user_id',
        'plan_id',
        'interval',
        'term',
        'bot_count',
        'trial_ends_at',
        'start_at',
        'end_at',
        'active',
        'last_invoice',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function plan()
    {
        return $this->hasOne('App\Models\BillingPlan', 'id', 'plan_id');
    }
}
