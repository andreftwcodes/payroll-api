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

        if ($this->timeCalc()->isLate()) {
            $remark = "Late : {$this->getLateTime()} mins";
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

    protected function getLateTime()
    {
        return ($this->timeCalc()->getWorkingHours() - $this->timeCalc()->getHours()) * 60;
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

    protected function getFormattedTime($time)
    {
        if (is_null($time)) {
            return;
        }

        return Carbon::parse($time)->format('h:i A');
    }
}
