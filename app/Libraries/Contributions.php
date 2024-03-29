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

            $yearFrom = Carbon::parse($this->date)->format('Y');

            if ($yearFrom === '2019') {

                if ($this->basicRate <= 10000) {
                    $amount = 275;
                } elseif ($this->basicRate >= 10000.01 && $this->basicRate <= 49999.99) {
                    $amount = $this->basicRate * .0275;
                } elseif ($this->basicRate >= 50000) {
                    $amount = 1375;
                }

            } elseif ($yearFrom === '2020') {

                if ($this->basicRate <= 10000) {
                    $amount = 300;
                } elseif ($this->basicRate >= 10000.01 && $this->basicRate <= 59999.99) {
                    $amount = $this->basicRate * .03;
                } elseif ($this->basicRate >= 60000) {
                    $amount = 1800;
                }

            } elseif ($yearFrom === '2021') {

                if ($this->basicRate <= 10000) {
                    $amount = 350;
                } elseif ($this->basicRate >= 10000.01 && $this->basicRate <= 69999.99) {
                    $amount = $this->basicRate * .035;
                } elseif ($this->basicRate >= 70000) {
                    $amount = 2450;
                }

            } elseif ($yearFrom === '2022') {

                if ($this->basicRate <= 10000) {
                    $amount = 400;
                } elseif ($this->basicRate >= 10000.01 && $this->basicRate <= 79999.99) {
                    $amount = $this->basicRate * .04;
                } elseif ($this->basicRate >= 80000) {
                    $amount = 3200;
                }

            } elseif ($yearFrom === '2023') {

                if ($this->basicRate <= 10000) {
                    $amount = 450;
                } elseif ($this->basicRate >= 10000.01 && $this->basicRate <= 89999.99) {
                    $amount = $this->basicRate * .045;
                } elseif ($this->basicRate >= 90000) {
                    $amount = 4050;
                }

            } elseif ($yearFrom === '2024' || $yearFrom === '2025') {

                if ($this->basicRate <= 10000) {
                    $amount = 500;
                } elseif ($this->basicRate >= 10000.01 && $this->basicRate <= 99999.99) {
                    $amount = $this->basicRate * .05;
                } elseif ($this->basicRate >= 100000) {
                    $amount = 5000;
                }

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