<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateLog extends Model
{
    protected $fillable = [
        'employee_id', 'amount',
    ];
}
