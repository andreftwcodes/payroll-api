<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaySlip extends Model
{
    protected $table = 'payslips';

    protected $fillable = [
        'to', 'from', 'contributions'
    ];

    public function payslip_periods()
    {
        return $this->hasMany(PayslipPeriod::class, 'payslip_id');
    }

    public function ca_children()
    {
        return $this->hasOne(CA_CHILDREN::class, 'payslip_id');
    }

    public function sss_loan_payment()
    {
        return $this->hasOne(SSSLoanPayment::class, 'payslip_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
