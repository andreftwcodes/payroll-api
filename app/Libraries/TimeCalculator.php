<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Traits\TimeScheduleTrait;

class TimeCalculator
{
    use TimeScheduleTrait;
    
    const LATE_ALLOWANCE = 15;

    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $this->mappedData($data);
    }

    public function getHours()
    {
        return $this->computeHoursWorked();
    }

    public function getOverTime()
    {
        return $this->computeOverTime();
    }

    public function getShift()
    {
        return $this->data['shift'];
    }

    protected function computeHoursWorked()
    {
        return $this->firstQuarter() + $this->secondQuarter();
    }

    protected function computeOverTime()
    {
        $hours = 0;

        if ($this->canOverTime() && $this->timeOutExceeded()) {
            $hours = Carbon::parse($this->data['sched_end_2'])
                ->floatDiffInHours($this->data['timeOut']);
        }

        return $hours;
    }

    protected function firstQuarter()
    {
        $sched_end_1 = $this->data['sched_end_1'];

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
        $sched_start_2 = $this->data['sched_start_2'];

        if (strtotime($this->timeOut()) < strtotime($sched_start_2)) {
            return 0;
        }

        if (strtotime($time_in = $this->timeIn()) > strtotime($sched_start_2)) {
            $sched_start_2 = $time_in;
        }

        return Carbon::parse($sched_start_2)
            ->floatDiffInHours($this->timeOut());
    }

    protected function timeIn()
    {
        return $this->isLate() ? $this->data['timeIn'] : $this->data['sched_start_1'];
    }

    protected function timeOut()
    {
        return $this->timeOutExceeded() ? $this->data['sched_end_2'] : $this->data['timeOut'];
    }

    protected function isLate()
    {
        $timeStart = Carbon::createFromTimeString($this->data['sched_start_1'])
                        ->addMinutes(self::LATE_ALLOWANCE);

        return strtotime($this->data['timeIn']) > strtotime($timeStart);
    }

    protected function timeOutExceeded()
    {
        return strtotime($this->data['timeOut']) > strtotime($this->data['sched_end_2']);
    }

    protected function canOverTime()
    {
        return (bool) $this->data['overtime'];
    }

}
