<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Other extends Model
{
    protected $fillable = [
        'contributions', 'night_shift', 'overtime'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($other) {
            $other->contributions = (int) filter_var($other->contributions, FILTER_VALIDATE_BOOLEAN);
            $other->night_shift = (int) filter_var($other->night_shift, FILTER_VALIDATE_BOOLEAN);
            $other->overtime = (int) filter_var($other->overtime, FILTER_VALIDATE_BOOLEAN);
        });
    }
}
