<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Other extends Model
{
    protected $fillable = [
        'special_person', 'night_shift', 'overtime'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($other) {
            $other->special_person = (int) filter_var($other->special_person, FILTER_VALIDATE_BOOLEAN);
            $other->night_shift = (int) filter_var($other->night_shift, FILTER_VALIDATE_BOOLEAN);
            $other->overtime = (int) filter_var($other->overtime, FILTER_VALIDATE_BOOLEAN);
        });
    }
}
