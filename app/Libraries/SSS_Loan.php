<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Libraries\SSS_Loan;
use App\Models\SSS_Loan as SSS_Loan_Model;

class SSS_Loan
{
    protected $id;

    protected $sss_loan = null;

    public function __construct($id = null)
    {
        $this->id = $id;
        $this->sss_loan = $this->_initSSSLoan();
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
        if (is_null($this->sss_loan)) {
           return 0;
        }

        return $this->sss_loan->amortization_amount;
    }

    public static function canDeduct($loaned_at)
    {
        return today()->greaterThanOrEqualTo(
            Carbon::parse($loaned_at)->addMonths(2)->startOfMonth() //Deduct after 1 months of loaned_at
        );
    }

    public static function canNotDeduct($loaned_at)
    {
        return !self::canDeduct($loaned_at);
    }

    private function _initSSSLoan()
    {
        if (!is_null($id = $this->id)) {
            if ($loan = SSS_Loan_Model::find($id)) {
                if (self::canDeduct($loan->loaned_at)) {
                    return $loan;
                }
            }
        }
    }

}