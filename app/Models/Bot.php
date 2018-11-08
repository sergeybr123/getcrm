<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    protected $fillable = [
        'botable_id',
        'botable_type',
        'name',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function company()
    {
        $company = $this->hasOne('App\Models\Company', 'id', 'botable_id');
        return $company;
    }
}
