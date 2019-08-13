<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SSSLoanPayment extends Model
{
    protected $table = 'sss_loan_payments';

    protected $fillable = [
        'loan_no', 'payslip_id', 'paid_at'
    ];
}
