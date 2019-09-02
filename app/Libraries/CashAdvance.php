<?php

namespace App\Libraries;

use App\Libraries\CashAdvance;

class CashAdvance
{
    protected $ad;

    public function __construct($ad = 0)
    {
        $this->ad = $ad;
    }

    public function getDataList()
    {
        return array(
            array(
                'name'   => 'Cash Advance',
                'amount' => number_format($this->getAmountDeductible(), 2)
            )
        );
    }

    public function getAmountDeductible()
    {
        return set_precision($this->ad, 2);
    }
}