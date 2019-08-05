<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'start_1', 'end_1', 'start_2', 'end_2', 'shift', 'status'
    ];
    
}
