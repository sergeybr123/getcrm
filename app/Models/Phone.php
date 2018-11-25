<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Phone extends Model
{
    protected $fillable = [
        'cca2',
        'country_code',
        'phone',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
