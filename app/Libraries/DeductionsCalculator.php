<?php
/**
 * Constructor Parameters
 * Parameter 1 [Grosspay] (floatval)
 * Parameter 2 [deductions id] (array)
 */

namespace App\Libraries;

use App\Models\sss_table_contribution;
use App\Models\pagibig_table_contribution;
use App\Models\philhealth_table_contribution;

class DeductionsCalculator
{
    protected $grossPay;

    protected $deductions;

    protected $sss = null;

    protected $pagibig = null;

    protected $philhealth = null;

    public function __construct($grossPay = 0, array $deductions = [])
    {
        $this->grossPay = $grossPay;
        $this->deductions = array_unique($deductions);
        $this->setDeductions();
    }
    
    public function getSSSER()
    {
        return !is_null($this->sss) ? $this->sss->er : 0;
    }

    public function getPagIbigER()
    {
        $amount = 0;

        if (!is_null($this->pagibig)) {
            $amount = $this->grossPay * ($this->pagibig->er / 100);
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
            $amount = $this->grossPay * ($this->pagibig->ee / 100);
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

        if ($this->grossPay > 0) {
            $amount =  $this->getSSSER() + $this->getPhilHealthER() + $this->getPagIbigER();
        }

        return $amount;
    }

    public function getEmployeeShareAmount()
    {
        $amount = 0;

        if ($this->grossPay > 0) {
            $amount = $this->getSSSEE() + $this->getPhilHealthEE() + $this->getPagIbigEE();
        }
        
        return $amount;
    }

    protected function setDeductions()
    {
        foreach ($this->deductions as $key => $id):
            switch ($id) {
                case 1: //id 1 for [sss,philh,pagibig]
                    $this->setMandatoryContributions();
                    break;
                
                default:
                    # code...
                    break;
            }
        endforeach;
    }

    protected function setMandatoryContributions()
    {
        $this->sss = sss_table_contribution::where(
            $this->mappedFilter()
        )->first();

        $this->pagibig = pagibig_table_contribution::where(
            $this->mappedFilter()
        )->first();

        $this->philhealth = philhealth_table_contribution::where(
            $this->mappedFilter()
        )->first();
    }

    protected function mappedFilter()
    {
        return [
            ['from', '<=', $this->grossPay],
            ['to', '>=', $this->grossPay],
        ];
    }

}