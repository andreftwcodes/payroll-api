<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\GovernmentLoan;

class Loan
{
    protected $ids;

    protected $loan = null;

    public function __construct($ids)
    {
        $this->ids   = $ids;
        $this->loans = $this->_loans();
    }

    public function getDataList()
    {
        if (is_null($loans = $this->loans)) {
            return array();
        }

        return $loans->map(function ($item, $key) {
            return array(
                'name'   => strtoupper($item->subject) . " Loan",
                'amount' => number_format($item->amortization, 2)
            );
        });
    }

    public function getAmountDeductible()
    {
        if (is_null($loans = $this->loans)) {
            return 0;
        }

        return $loans->sum('amortization');
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

    private function _loans()
    {
        if (!empty($ids = $this->ids)) {
            if ($loans = GovernmentLoan::whereIn('id', $ids)->get()) {
                return $loans->filter(function ($item, $key) {
                    return self::canDeduct($item->loaned_at);
                });
            }
        }
    }

}