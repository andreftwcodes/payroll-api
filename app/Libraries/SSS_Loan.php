<?php

namespace App\Libraries;

use App\Libraries\SSS_Loan;

class SSS_Loan
{
    protected $amount;

    public function __construct($amount = 0)
    {
        $this->amount = $amount;
    }

    public function getDataList()
    {
        return array(
            array(
                'name'   => 'SSS Loan',
                'amount' => number_format($this->getAmountDeductible(), 2)
            )
        );
    }

    public function getAmountDeductible()
    {
        return $this->amount;
    }
}