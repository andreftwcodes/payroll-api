<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SSSLoanTrait
{
    protected function balance()
    {
        $balance = $this->amount;

        if ($payments = $this->payments()) {
            if ($count = $payments->count()) {
                $balance = $this->amount - ($this->deductibleAmount() * $count);
            }
        }

        return $balance;
    }

    protected function progress()
    {
        return ceil(($this->payments()->count() / 24) * 100);
    }

    protected function payments()
    {
        return $this->whenLoaded('sss_loan_payments');
    }

    protected function deductibleAmount()
    {
        return $this->amount / 24;
    }

    protected function formattedAmount($value)
    {
        return number_format($value, 2);
    }
}
