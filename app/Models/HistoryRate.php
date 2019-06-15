<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryRate extends Model
{
    protected $table = 'history_rates';

    protected $fillable = [
        'employee_id', 'amount'
    ];
}
