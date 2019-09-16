<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SSSLoanPayment extends Model
{
    protected $table = 'sss_loan_payments';

    protected $fillable = [
        'payslip_id', 'paid_at'
    ];

    public function sss_loan()
    {
        return $this->belongsTo(SSS_Loan::class, 'sss_loan_id');
    }
}
