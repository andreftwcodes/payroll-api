<?php
/**
 * Constructor Parameters
 * Parameter 1 [basicRate] (floatval)
 * Parameter 2 [checker] (Boolean)
 */

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\sss_table_contribution as SSS;
use App\Models\pagibig_table_contribution as PagIbig;
use App\Models\philhealth_table_contribution as PhilHealth;

class MandatoryDeductions
{
    protected $basicRate;

    protected $checker;

    protected $sss = null;

    protected $pagibig = null;

    protected $philhealth = null;

    protected $widthHoldingTax = null;

    protected $others = null;

    public function __construct($basicRate = 0, $checker = false)
    {
        $this->basicRate = $basicRate;
        $this->checker = $checker;
        $this->setDeductions();
    }

    public function getAmount()
    {
        return $this->getEmployeeShareAmount();
    }
    
    public function getSSSER()
    {
        return !is_null($this->sss) ? $this->sss->er : 0;
    }

    public function getPagIbigER()
    {
        $amount = 0;

        if (!is_null($this->pagibig)) {
            // $amount = $this->basicRate * ($this->pagibig->er / 100);
            $amount = 100;
        }

        return $amount;
    }

    public function getPhilHealthER()
    {
        return !is_null($this->philhealth) ? $this->philhealth->er : 0;
    }
    
    public function getSSSEE()
    {
        return !is_null($this->sss) ? $this->sss->ee : 0;
    }
    
    public function getPagIbigEE()
    {
        $amount = 0;

        if (!is_null($this->pagibig)) {
            // $amount = $this->basicRate * ($this->pagibig->ee / 100);
            $amount = 100;
        }

        return $amount;
    }

    public function getPhilHealthEE()
    {
        return !is_null($this->philhealth) ? $this->philhealth->ee : 0;
    }
    
    public function getEmployerShareAmount()
    {
        $amount = 0;

        if ($this->basicRate > 0) {
            $amount =  $this->getSSSER() + $this->getPhilHealthER() + $this->getPagIbigER();
        }

        return $amount;
    }

    public function getEmployeeShareAmount()
    {
        $amount = 0;

        if ($this->basicRate > 0) {
            $amount = $this->getSSSEE() + $this->getPhilHealthEE() + $this->getPagIbigEE() + $this->getWithHoldingTax() + $this->getOthers();
        }
        
        return $amount;
    }

    protected function getWithHoldingTax()
    {
        return $this->widthHoldingTax;
    }

    protected function getOthers()
    {
        return $this->others;
    }

    public function getDataList()
    {
        return array(
            array(
                'name'   => 'SSS',
                'amount' => number_format($this->getSSSEE(), 2)
            ),
            array(
                'name'   => 'PhilHealth',
                'amount' => number_format($this->getPhilHealthEE(), 2)
            ),
            array(
                'name'   => 'PagIbig',
                'amount' => number_format($this->getPagIbigEE(), 2)
            ),
            array(
                'name'   => 'Withholding Tax',
                'amount' => number_format($this->getWithHoldingTax(), 2)
            ),
            array(
                'name'   => 'Others',
                'amount' => number_format($this->getOthers(), 2)
            ),
        );
    }

    protected function setDeductions()
    {
        if ($this->isDateOfDeduction() && $this->canDeduct()) {
            
            $filter = array(
                array('from', '<=', $this->basicRate),
                array('to', '>=', $this->basicRate),
            );
    
            $this->sss = SSS::where($filter)->first();
    
            $this->pagibig = PagIbig::where($filter)->first();
    
            $this->philhealth = PhilHealth::where($filter)->first();
            
        }

        $this->widthHoldingTax = 0; //@brb

        $this->others = 0; //@brb
    }

    protected function isDateOfDeduction()
    {
        $date = Carbon::now()->endOfMonth();

        if (!$date->isSaturday()) {
            $date->previous(Carbon::SATURDAY);
            // $date->next(Carbon::SATURDAY);
        }

        return $date->toDateString() === Carbon::now()->toDateString();
    }

    protected function canDeduct()
    {
        return $this->checker === true;
    }

}