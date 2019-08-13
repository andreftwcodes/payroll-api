<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SSS_Loan extends Model
{
    protected $table = 'sss_loans';

    protected $fillable = [
        'loan_no', 'amount', 'loaned_at'
    ];

    public function sss_loan_payments()
    {
        return $this->hasMany(SSSLoanPayment::class, 'loan_no', 'loan_no');
    }
}
