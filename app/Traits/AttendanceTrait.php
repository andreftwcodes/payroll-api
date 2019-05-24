<?php

namespace App\Traits;

use Carbon\Carbon;

trait AttendanceTrait
{
    protected function getRemark()
    {
        $remark = 'On Time';

        if (is_null($this->start)) {
           return 'Absent';
        }

        if ($this->getExceedTime() > $this->getLateAllowance()) {
            $remark = "Late : {$this->getExceedTime()} mins";
        }

        return $remark;
    }

    protected function getExceedTime()
    {
        $parsedStart = Carbon::parse(
            $start = $this->start
        );

        $parsedScheduleStart = Carbon::parse(
            $scheduleStart = $this->sched_start_1
        );

        if ($parsedScheduleStart->greaterThan($parsedStart)) {
            return 0;
        }

        return Carbon::parse($start)
                ->diffInMinutes($scheduleStart);
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
