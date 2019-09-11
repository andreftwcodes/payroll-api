<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SSSLoanTrait
{
    protected $payment_terms = 24; //months

    protected function balance()
    {
        $balance = $this->amount_loaned;

        if ($payments = $this->payments()) {
            if ($count = $payments->count()) {
                $balance = $this->amount_loaned - ($this->deductibleAmount() * $count);
            }
        }

        return $balance;
    }

    protected function progress()
    {
        return ceil(($this->payments()->count() / $this->payment_terms) * 100);
    }

    protected function payments()
    {
        return $this->sss_loan_payments;
    }

    protected function deductibleAmount()
    {
        return $this->amortization_amount;
    }

    protected function formattedAmount($value)
    {
        return number_format($value, 2);
    }
}
