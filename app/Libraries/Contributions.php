<?php
/**
 * Constructor Parameters
 * Parameter 1 [basicRate] (floatval)
 * Parameter 2 [date] (Date)
 * Parameter 3 [deduct] (Boolean)
 */

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\hdr_contribution as Contribution;

class Contributions
{
    protected $basicRate;
    
    protected $date;

    protected $deduct;

    protected $sss = null;

    protected $pagibig = null;

    protected $philhealth = null;

    public function __construct($basicRate = 0, $date = null, $deduct = false)
    {
        $this->basicRate = $basicRate;
        $this->date      = $date;
        $this->deduct    = $deduct;
        $this->setDeductions();
    }

    public function getAmount()
    {
        return $this->getEmployeeShareAmount();
    }
    
    public function getSSSER()
    {
        $amount = 0;
        
        if ($this->canDeduct()) {
            $amount = !is_null($this->sss) ? $this->sss->er : 0;
        }

        return $amount;
    }

    public function getPagIbigER()
    {
       return $this->canDeduct() ? 100 : 0;
    }

    public function getPhilHealthER()
    {
        $amount = 0;

        if ($this->canDeduct()) {
            if ($this->basicRate <= 10000) {
                $amount = 275;
            } elseif ($this->basicRate >= 10000.01 && $this->basicRate <= 39999.99) {
                $amount = $this->basicRate * .0275;
            } elseif ($this->basicRate >= 40000) {
                $amount = 1100;
            }
        }

        return $amount / 2;
    }
    
    public function getSSSEE()
    {
        $amount = 0;
        
        if ($this->canDeduct()) {
            $amount = !is_null($this->sss) ? $this->sss->ee : 0;
        }

        return $amount;
    }
    
    public function getPagIbigEE()
    {
        return $this->getPagIbigER();
    }

    public function getPhilHealthEE()
    {
        return $this->getPhilHealthER();
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
            $amount = $this->getSSSEE() + $this->getPhilHealthEE() + $this->getPagIbigEE();
        }
        
        return $amount;
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
            )
        );
    }

    public function canDeduct()
    {
        return $this->deduct === 'true' || $this->deduct == 1;
    }

    protected function setDeductions()
    {

        $sss = Contribution::sss()->usedAt($this->date)->first();

        if (!is_null($sss)) {
            $this->sss = $sss->ranges()
                ->applyFilter($this->basicRate)
                    ->first();
        }

    }

}