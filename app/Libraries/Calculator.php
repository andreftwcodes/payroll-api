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
            Carbon::parse($this->timeIn())
                ->floatDiffInHours($this->timeOut())
        );
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
        return $this->isLate() ? $this->request->timeIn : $this->request->start;
    }

    protected function timeOut()
    {
        return $this->timeOutExceeded() ? $this->request->end : $this->request->timeOut;
    }

    protected function isLate()
    {
        $timeStart = Carbon::createFromTimeString($this->request->start)
                        ->addMinutes(self::LATE_ALLOWANCE);

        return strtotime($this->request->timeIn) > strtotime($timeStart);
    }

    protected function timeOutExceeded()
    {
        return strtotime($this->request->timeOut) > strtotime($this->request->end);
    }

    protected function canOverTime()
    {
        return (bool) $this->request->overtime;
    }

    protected function isNightShift()
    {
        return (bool) $this->request->night_shift;
    }

    public function overTime()
    {
        $grossPay = 0;

        if ($this->canOverTime() && $this->timeOutExceeded()) {

            $minutesWorked = $this->toMinutes(
                Carbon::parse($this->request->end)->floatDiffInHours($this->request->timeOut)
            );
    
            $grossPay = $minutesWorked * $this->ratePerMinute();

            if ($this->isNightShift()) {
                $grossPay += $this->nightShift($minutesWorked);
            }

            $grossPay *= (self::OVERTIME_RATE / 100);
        }

        return $grossPay;
    }

    public function nightShift($minutesWorked = null)
    {
        $amount = 0;

        if ($this->isNightShift()) {
            $amount = $this->ratePerMinute() * (self::NIGHT_DIFFERENTIAL / 100);
            $amount *= !is_null($minutesWorked) ? $minutesWorked : $this->minutesWorked(); 
        }

        return $amount;
    }

}
