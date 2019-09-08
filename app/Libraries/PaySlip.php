<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Libraries\SSS_Loan;
use Illuminate\Http\Request;
use App\Libraries\Calculator;
use App\Libraries\CashAdvance;
use App\Libraries\Contributions;
use App\Libraries\TimeCalculator;

class PaySlip
{
    protected $request;

    protected $employee;

    protected $attendances;

    protected $grossPay = 0;

    protected $overTimeHours = 0;

    protected $underTimeHours = 0;

    protected $overTimePay = 0;

    protected $underTimePay = 0;

    protected $deducAmount = 0;

    protected $dataList = array();

    const CURRENCY = 'PHP ';

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

    public function getPrintUrl()
    {
        return url('api/payslip/pdf', [$this->printParamsSecretKey()]);
    }

    protected function attendanceDataSet($date_period = null)
    {
        return Attendance::with('time_logs')
            ->where('employee_id', $this->employee->id)
                ->applyDateFilterPeriod(!is_null($date_period) ? $date_period : $this->request)
                    ->get();
    }

    protected function setAttributes()
    {
        foreach ($this->attendanceDataSet() as $key => $attendance):

            $timeCalc = $this->__TimeCalculator($attendance);

            $calc = $this->__Calculator($attendance, $timeCalc);
    
            $this->grossPay      += $calc->getGrossPay();
            $this->overTimeHours += $calc->getOverTimeHours(); //@brb
            $this->overTimePay   += $calc->overTimePay();

            $this->underTimeHours += $calc->getUnderTimeHours(); //@brb
            $this->underTimePay   += $calc->underTimePay();

        endforeach;

        $this->setDeductions();

    }

    protected function plottedData()
    {
        return [
            'data' => [
                'fullname'         => $this->fullname(),
                'period'           => $this->datePeriod(),
                'date_issued'      => $this->dateIssued(),
                'basic_rate'       => $this->getFormatted($this->basicRate()),
                'overtime'         => $this->overTime(),
                'undertime'        => $this->underTime(),
                'gross_pay'        => $this->getFormatted($this->grossPay()),
                'less'             => $this->dataList,
                'total_deductions' => $this->getFormatted($this->totalDeductionAmount()),
                'net_pay'          => self::CURRENCY . $this->getFormatted($this->netPay())
            ],
            'extra' => [
                'days'      => $this->daysCount(),
                'print_url' => $this->getPrintUrl()
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
        return $this->attendanceDataSet()->sum('amount'); //sum by period
        // return ($this->employee->rate * 313) / 12;
    }

    protected function overTime()
    {
        return [
            'hours'  => number_format($this->overTimeHours, 2, '.', ''),
            'amount' => number_format($this->overTimePay, 2)
        ];
    }

    protected function underTime()
    {
        return [
            'hours'  => number_format($this->underTimeHours, 2, '.', ''),
            'amount' => number_format($this->underTimePay, 2)
        ];
    }

    protected function grossPay()
    {
        return round($this->grossPay, 2);
    }

    protected function totalDeductionAmount()
    {
        return round($this->deducAmount, 2);
    }

    protected function netPay()
    {
        return $this->grossPay() - $this->totalDeductionAmount();
    }

    protected function daysCount()
    {
        return $this->attendanceDataSet()->count();
    }

    protected function printParamsSecretKey()
    {
        return base64_encode(
            collect([
                'employee_id'   => $this->employee->id,
                'from'          => $this->request->from,
                'to'            => $this->request->to,
                'contributions' => $this->request->contributions,
                'ca_amount_deductible' => $this->request->ca_amount_deductible,
                'sss_loan_id' => $this->request->sss_loan_id
            ])->toJson()
        );
    }

    protected function setDeductions()
    {
        $contributions = new Contributions(
            $this->getMonthlyGrossPay(),
            $this->request->from,
            $this->request->contributions
        );

        $cash_advance = new CashAdvance(
            $this->request->ca_amount_deductible
        );

        $sss_loan = new SSS_Loan(
            $contributions->canDeduct() ? $this->request->sss_loan_id : null
        );

        $dataList = collect([
            $contributions->getDataList(),
            $cash_advance->getDataList(),
            $sss_loan->getDataList()
        ]);

        collect($dataList->flatten(1)->toArray())->each(function ($item, $key) {
            array_push($this->dataList, $item);
        });

        $this->deducAmount += $contributions->getEmployeeShareAmount() + $cash_advance->getAmountDeductible() + $sss_loan->getAmountDeductible();
    }

    protected function getFormatted($value)
    {
        return number_format($value, 2);
    }

    protected function getMonthlyGrossPay()
    {
        $dt = Carbon::parse($this->request->from);

        $attendances = $this->attendanceDataSet(
            new Request([
                'from' => $dt->startOfMonth()->toDateString(),
                'to'   => $dt->endOfMonth()->toDateString(),
            ])
        );

        $grossPay = 0;

        foreach ($attendances as $key => $attendance) {
            
            $timeCalc = $this->__TimeCalculator($attendance);

            $calc = $this->__Calculator($attendance, $timeCalc);

            $grossPay += $calc->getGrossPay();

        }

        return round($grossPay, 2);
    }

    private function __TimeCalculator($attendance)
    {
        return new TimeCalculator([
            'sched_start_1' => $attendance['sched_start_1'],
            'sched_end_1'   => $attendance['sched_end_1'],
            'sched_start_2' => $attendance['sched_start_2'],
            'sched_end_2'   => $attendance['sched_end_2'],
            'time_logs'     => $attendance->time_logs()->get()
        ]);
    }

    private function __Calculator($attendance, $timeCalc)
    {
        return new Calculator([
            'rate'          => $attendance['amount'],
            'working_hours' => $timeCalc->getWorkingHours(),
            'hours_worked'  => $timeCalc->getHours(),
            'night_shift_hours_worked' => $timeCalc->getNightShiftWorkedHours(),
            'overtime'      => $attendance['overtime'],
            'night_shift'   => $attendance['night_shift']
        ]);
    }
}