<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GovernmentLoanPayment extends Model
{
    protected $table = 'government_loan_payments';

    protected $fillable = [
        'payslip_id', 'paid_at'
    ];

    public function government_loan()
    {
        return $this->belongsTo(GovernmentLoan::class, 'government_loan_id');
    }
}
