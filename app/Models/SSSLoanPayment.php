<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SSSLoanPayment extends Model
{
    protected $table = 'sss_loan_payments';

    protected $fillable = [
        'payslip_id', 'paid_at'
    ];
}
