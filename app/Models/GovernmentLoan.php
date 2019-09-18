<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GovernmentLoan extends Model
{
    protected $table = 'government_loans';

    protected $fillable = [
        'ref_no', 'subject', 'amount_loaned', 'amortization', 'loaned_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function government_loan_payments()
    {
        return $this->hasMany(GovernmentLoanPayment::class, 'government_loan_id');
    }
}
