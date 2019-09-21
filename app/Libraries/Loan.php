<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\GovernmentLoan;

class Loan
{
    protected $id;

    protected $loan = null;

    public function __construct($id)
    {
        $this->id   = $id;
        $this->loan = $this->_initLoan();
    }

    public function getDataList()
    {
        if (is_null($loan = $this->loan)) {
            return array();
        }

        return $loan->map(function ($item, $key) {
            return array(
                'name'   => strtoupper($item->subject) . " Loan",
                'amount' => number_format($item->amortization, 2)
            );
        });
    }

    public function getAmountDeductible()
    {
        if (is_null($loan = $this->loan)) {
            return 0;
        }

        return $loan->sum('amortization');
    }

    public static function canDeduct($loaned_at)
    {
        /**
         * The monthly amortization shall start on 2nd month following the date of loan.
        */

        return today()->greaterThanOrEqualTo(
            Carbon::parse($loaned_at)->addMonths(2)->startOfMonth()
        );
    }

    public static function canNotDeduct($loaned_at)
    {
        return !self::canDeduct($loaned_at);
    }

    private function _initLoan()
    {
        if (!empty($id = $this->id)) {
            if ($loans = GovernmentLoan::whereIn('id', $id)->get()) {
                return $loans->filter(function ($item, $key) {
                    return self::canDeduct($item->loaned_at);
                });
            }
        }
    }

}