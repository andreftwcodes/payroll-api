<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLogs extends Model
{
    protected $table = 'time_logs';

    public $timestamps = false;

    protected $fillable = [
        'time_in', 'time_out'
    ];
}
