<?php

namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Http\Request;

class Calculator
{
    const LATE_ALLOWANCE = 15;

    const WORKING_HOURS = 8;

    const OVERTIME_RATE = 130;

    const NIGHT_DIFFERENTIAL = 10;

    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function getGrossPay()
    {
        return $this->computeGrossPay() + $this->nightShift() + $this->overTime();
    }

    public function getFormattedGrossPay()
    {
        return '₱ ' . number_format($this->getGrossPay(), 2);
    }

    protected function computeGrossPay()
    {
        return $this->minutesWorked() * $this->ratePerMinute();
    }

    protected function minutesWorked()
    {
        return $this->toMinutes(
            $this->firstQuarter() + $this->secondQuarter()
        );
    }

    protected function firstQuarter()
    {
        $sched_end_1 = $this->request->sched_end_1;

        if (strtotime($this->timeIn()) > strtotime($sched_end_1)) {
            return 0;
        }

        if (strtotime($time_out = $this->timeOut()) < strtotime($sched_end_1)) {
            $sched_end_1 = $time_out;
        }

        return Carbon::parse($this->timeIn())
            ->floatDiffInHours($sched_end_1);
    }

    protected function secondQuarter()
    {
        $sched_start_2 = $this->request->sched_start_2;

        if (strtotime($this->timeOut()) < strtotime($sched_start_2)) {
            return 0;
        }

        if (strtotime($time_in = $this->timeIn()) > strtotime($sched_start_2)) {
            $sched_start_2 = $time_in;
        }

        return Carbon::parse($sched_start_2)
            ->floatDiffInHours($this->timeOut());
    }

    protected function ratePerMinute()
    {
        return $this->request->rate / $this->toMinutes(self::WORKING_HOURS);
    }

    protected function toMinutes($value)
    {
        return $value * 60;
    }

    protected function timeIn()
    {
        return $this->isLate() ? $this->request->timeIn : $this->request->sched_start_1;
    }

    protected function timeOut()
    {
        return $this->timeOutExceeded() ? $this->request->sched_end_2 : $this->request->timeOut;
    }

    protected function isLate()
    {
        $timeStart = Carbon::createFromTimeString($this->request->sched_start_1)
                        ->addMinutes(self::LATE_ALLOWANCE);

        return strtotime($this->request->timeIn) > strtotime($timeStart);
    }

    protected function timeOutExceeded()
    {
        return strtotime($this->request->timeOut) > strtotime($this->request->sched_end_2);
    }

    protected function canOverTime()
    {
        return (bool) $this->request->overtime;
    }

    protected function isNightShift()
    {
        return (bool) $this->request->night_shift;
    }

    protected function overTime()
    {
        $grossPay = 0;

        if ($this->canOverTime() && $this->timeOutExceeded()) {

            $minutesWorked = $this->toMinutes(
                Carbon::parse($this->request->sched_end_2)->floatDiffInHours($this->request->timeOut)
            );
    
            $grossPay = $minutesWorked * $this->ratePerMinute();

            if ($this->isNightShift()) {
                $grossPay += $this->nightShift($minutesWorked);
            }

            $grossPay *= (self::OVERTIME_RATE / 100);
        }

        return $grossPay;
    }

    protected function nightShift($minutesWorked = null)
    {
        $amount = 0;

        if ($this->isNightShift()) {
            $amount = $this->ratePerMinute() * (self::NIGHT_DIFFERENTIAL / 100);
            $amount *= !is_null($minutesWorked) ? $minutesWorked : $this->minutesWorked(); 
        }

        return $amount;
    }

}
