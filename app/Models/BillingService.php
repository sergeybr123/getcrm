<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingService extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'plan_id',
        'name',
        'description',
        'price',
        'active',
    ];

    protected $dates = [
        'start_at',
        'end_at',
        'deleted_at',
    ];
}
