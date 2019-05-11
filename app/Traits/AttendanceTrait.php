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
            $scheduleStart = $this->employee->schedule->start
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
}
