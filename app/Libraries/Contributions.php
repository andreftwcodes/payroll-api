<?php
/**
 * Constructor Parameters
 * Parameter 1 [basicRate] (floatval)
 * Parameter 2 [deduct] (Boolean)
 */

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\hdr_contribution as Contribution;

class Contributions
{
    protected $basicRate;

    protected $deduct;

    protected $sss = null;

    protected $pagibig = null;

    protected $philhealth = null;

    public function __construct($basicRate = 0, $deduct = false)
    {
        $this->basicRate = $basicRate;
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

    protected function setDeductions()
    {
        if ($this->canDeduct()) {

            $sss        = Contribution::sss()->first();
            $pagibig    = Contribution::pagibig()->first();
            $philhealth = Contribution::philhealth()->first();

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

    protected function canDeduct()
    {
        return $this->deduct === 'true';
    }

}