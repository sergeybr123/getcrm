<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'bot',
        'temp_bot',
        'name',
        'description',
        'cover_image',
        'avatar'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'bot' => 'array',
        'temp_bot' => 'array',
    ];

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }

    public function bots()
    {
        $bots = $this->hasMany('App\Models\Bot', 'botable_id', 'id') ?? null;
        return $bots;
    }
}
