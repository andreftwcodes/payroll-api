<?php

namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Http\Request;

class Calculator
{
    const LATE_ALLOWANCE = 15;

    const WORKING_HOURS = 8;

    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function getGrossPay()
    {
        return $this->computeGrossPay();
    }

    public function getFormattedGrossPay()
    {
        return number_format($this->getGrossPay(), 2);
    }

    protected function computeGrossPay()
    {
        return round($this->minutesWorked() * $this->ratePerMinute(), 2);
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
        return $this->request->dailyRate / $this->toMinutes(self::WORKING_HOURS);
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
        return strtotime($this->request->timeOut) > strtotime($this->request->end) ? $this->request->end : $this->request->timeOut;
    }

    protected function isLate()
    {
        $timeStart = Carbon::createFromTimeString($this->request->start)
                        ->addMinutes(self::LATE_ALLOWANCE);

        return strtotime($this->request->timeIn) > strtotime($timeStart);
    }

}
