<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BotListener;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bot extends Model
{
    use SoftDeletes;

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

    public function listeners() {
        return $this->hasMany(BotListener::class);
    }

    public function inputs() {
        return $this->hasMany(BotInput::class);
    }
}
