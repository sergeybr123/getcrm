<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BillingPlan;

class BillingSubscribe extends Model
{
    protected $connection = 'billing';

    protected $table = 'subscribes';

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

    public function plan()
    {
        return $this->hasOne('App\Models\BillingPlan', 'id', 'plan_id');
    }
}
