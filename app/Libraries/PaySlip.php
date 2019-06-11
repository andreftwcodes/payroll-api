<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Libraries\Calculator;
use App\Libraries\TimeCalculator;
use App\Libraries\MandatoryDeductions;

class PaySlip
{
    protected $request;

    protected $employee;

    protected $attendances;

    protected $grossPay;

    protected $overTimeHours;

    protected $overTimePay;

    protected $deducAmount = 0;

    protected $dataList = array();

    public function __construct($request = null, $employee = null)
    {
        $this->request = $request;
        $this->employee = $employee;
        $this->setAttributes();
    }

    public function getResult()
    {
        return $this->plottedData();
    }

    protected function attendanceDataSet()
    {
        return Attendance::where('employee_id', $this->employee->id)
            ->applyDateFilterPeriod($this->request)
                ->get();
    }

    protected function setAttributes()
    {
        foreach ($this->attendanceDataSet() as $key => $attendance):

            $timeCalc = new TimeCalculator([
                'sched_start_1' => $attendance['sched_start_1'],
                'sched_end_1'   => $attendance['sched_end_1'],
                'sched_start_2' => $attendance['sched_start_2'],
                'sched_end_2'   => $attendance['sched_end_2'],
                'timeIn'        => $attendance['start'],
                'timeOut'       => $attendance['end'],
            ]);

            $calc = new Calculator([
                'rate'         => $attendance['amount'],
                'hours_worked' => $timeCalc->getHours(),
                'overtime'     => $attendance['overtime'], //OT premium flag
                'shift'        => $timeCalc->getShift()
            ]);
    
            $this->grossPay += $calc->getGrossPay();
            $this->overTimeHours += $calc->getOverTimeHours(); //@brb
            $this->overTimePay += $calc->overTimePay();

        endforeach;

        $this->setMandatoryDeductions();

    }

    protected function plottedData()
    {
        return [
            'data' => [
                'fullname' => $this->fullname(),
                'period' => $this->datePeriod(),
                'date_issued' => $this->dateIssued(),
                'basic_rate' => $this->getFormatted($this->basicRateByPeriod()),
                'overtime' => $this->overTime(),
                'gross_pay' => $this->getFormatted($this->grossPay),
                'less' => $this->dataList,
                'total_deductions' => $this->getFormatted($this->totalDeductionAmount()),
                'net_pay' => $this->getFormatted($this->netPay())
            ],
            'extra' => [
                'days' => $this->daysCount(),
                'print_url' => url('api/payslip/pdf', [$this->printParamsSecretKey()])
            ]
        ];
    }

    protected function fullname()
    {
        return "{$this->employee->firstname} {$this->employee->middlename} {$this->employee->lastname}";
    }

    protected function datePeriod()
    {
        return "{$this->request->from} - {$this->request->to}";
    }

    protected function dateIssued()
    {
        return Carbon::now()->format('m-d-Y');
    }

    protected function basicRate()
    {
        return ($this->employee->rate->amount * 313) / 12;
    }

    protected function basicRateByPeriod()
    {
        return $this->employee->rate->amount * $this->daysCount();
    }

    protected function overTime()
    {
        return [
            'hours' => $this->overTimeHours,
            'amount' => $this->getFormatted($this->overTimePay)
        ];
    }

    protected function totalDeductionAmount()
    {
        return $this->deducAmount;
    }

    protected function daysCount()
    {
        return $this->attendanceDataSet()->count();
    }

    protected function printParamsSecretKey()
    {
        return base64_encode(
            collect([
                'employee_id' => $this->employee->id,
                'from' => $this->request->from,
                'to' => $this->request->to
            ])->toJson()
        );
    }

    protected function setMandatoryDeductions()
    {
        /**
         * id 1 for [sss,philh,pagibig]
        */
        
        $md = new MandatoryDeductions(
            $this->grossPay,
            $this->employee->deductions->contains(1)
        );

        collect($md->getDataList())->each(function ($item, $key) {
            array_push($this->dataList, $item);
        });

        $this->deducAmount += $md->getEmployeeShareAmount();
    }

    protected function netPay()
    {
        return round($this->grossPay - $this->totalDeductionAmount(), 2);
    }

    protected function getFormatted($value)
    {
        return number_format($value, 2);
    }
}