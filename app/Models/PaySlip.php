<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaySlip extends Model
{
    protected $table = 'payslips';

    protected $fillable = [
        'to', 'from', 'contributions'
    ];

    public function ca_children()
    {
        return $this->hasOne(CA_CHILDREN::class, 'payslip_id');
    }
}
