<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CA_CHILDREN extends Model
{
    protected $table = 'ca_childrens';

    protected $fillable = [
        'payslip_id', 'date', 'credit', 'debit'
    ];
}
