<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class employee_schedules extends Model
{
    protected $fillable = [
        'day', 'start_1', 'end_1', 'start_2', 'end_2', 'status'
    ];
}
