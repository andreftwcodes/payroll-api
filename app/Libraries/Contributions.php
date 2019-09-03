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
        $this->date = $date;
        $this->deduct = $deduct;
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
       return !is_null($this->pagibig) ? 100 : 0;
    }

    public function getPhilHealthER()
    {
        return !is_null($this->philhealth) ? ($this->basicRate * .0275) / 2 : 0;
    }
    
    public function getSSSEE()
    {
        return !is_null($this->sss) ? $this->sss->ee : 0;
    }
    
    public function getPagIbigEE()
    {
        return !is_null($this->pagibig) ? 100 : 0;
    }

    public function getPhilHealthEE()
    {
        return !is_null($this->philhealth) ? ($this->basicRate * .0275) / 2 : 0;
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
        if ($this->canDeduct()) {

            $sss        = Contribution::sss()->usedAt($this->date)->first();
            $pagibig    = Contribution::pagibig()->usedAt($this->date)->first();
            $philhealth = Contribution::philhealth()->usedAt($this->date)->first();

            if (!is_null($sss)) {
                $this->sss = $sss->ranges()
                    ->applyFilter($this->basicRate)
                    ->first();
            }

            if (!is_null($pagibig)) {
                $this->pagibig = $pagibig->ranges()
                    ->applyFilter($this->basicRate)
                    ->first();
            }

            if (!is_null($philhealth)) {
                $this->philhealth = $philhealth->ranges()
                    ->applyFilter($this->basicRate)
                    ->first();
            }

        }
    }

}