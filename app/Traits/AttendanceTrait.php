<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Libraries\TimeCalculator;

trait AttendanceTrait
{
    protected function timeIn()
    {
        return $this->time_logs()->get()->pluck('time_in')->first();
    }

    protected function timeOut()
    {
        return $this->time_logs()->get()->pluck('time_out')->last();
    }
    
    protected function getRemark()
    {
        $remark = 'On Time';

        if (is_null($this->timeIn())) {
           return 'Absent';
        }

        if ($this->getExceedTime() > $this->getLateAllowance()) {
            $remark = "Late : {$this->getExceedTime()} mins";
        }

        if ($hours = $this->timeCalc()->getHours()) {

            if ($hours < $this->timeCalc()->getWorkingHours()) {
                $remark .= ' / UT';
            }

            if ($hours > $this->timeCalc()->getWorkingHours()) {
                $remark .= ' / OT';
            }

        }

        return $remark;
    }

    protected function getExceedTime()
    {
        $parsedStart = Carbon::parse(
            $start = $this->timeIn()
        );

        $parsedScheduleStart = Carbon::parse(
            $scheduleStart = $this->sched_start_1
        );

        if ($parsedScheduleStart->greaterThan($parsedStart)) {
            return 0;
        }

        if ($parsedStart->greaterThanOrEqualTo(Carbon::parse($scheduleEnd = $this->sched_end_1))) {
            $start         = $this->sched_start_1;
            $scheduleStart = $scheduleEnd;
        }

        $minutes = 0;

        if ($parsedStart->greaterThan(Carbon::parse($scheduleStart2 = $this->sched_start_2))) {

            $minutes = Carbon::parse($parsedStart)
                ->diffInMinutes($scheduleStart2);

        }

        return Carbon::parse($start)
                ->diffInMinutes($scheduleStart) + $minutes;
    }

    protected function timeCalc()
    {
        return new TimeCalculator([
            'sched_start_1' => $this->sched_start_1,
            'sched_end_1'   => $this->sched_end_1,
            'sched_start_2' => $this->sched_start_2,
            'sched_end_2'   => $this->sched_end_2,
            'time_logs'     => $this->time_logs()->get()
        ]);
    }

    protected function getLateAllowance()
    {
        return 15;
    }

    protected function getFormattedTime($time)
    {
        if (is_null($time)) {
            return;
        }

        return Carbon::parse($time)->format('h:i A');
    }
}
