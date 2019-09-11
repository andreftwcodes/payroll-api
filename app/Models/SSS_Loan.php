<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SSS_Loan extends Model
{
    protected $table = 'sss_loans';

    protected $fillable = [
        'ref_no', 'amount_loaned', 'amortization_amount', 'loaned_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function sss_loan_payments()
    {
        return $this->hasMany(SSSLoanPayment::class, 'sss_loan_id');
    }
}
