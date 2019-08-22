<?php

namespace App\Libraries;

use Carbon\Carbon;

class TimeCalculator
{
    const LATE_ALLOWANCE = 15;

    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getHours()
    {
        return $this->computeHoursWorked();
    }

    public function getWorkingHours()
    {
        $firstQuarter = Carbon::parse($this->data['sched_start_1'])
            ->floatDiffInHours($this->data['sched_end_1']);

        $secondQuarter = Carbon::parse($this->data['sched_start_2'])
            ->floatDiffInHours($this->data['sched_end_2']);

        return $firstQuarter + $secondQuarter;
    }

    protected function computeHoursWorked()
    {
        if (empty($this->data['time_logs'])) {
            return 0;
        }

        return $this->firstQuarter() + $this->secondQuarter();
    }

    protected function firstQuarter()
    {
        $hours = 0;

        $sched_end_1 = $this->data['sched_end_1'];

        foreach ($this->mappedTimeLogs() as $key => $item) {

            if (is_null($item['time_in']) || is_null($item['time_out'])) {
                continue;
            }

            if (strtotime($item['time_in']) > strtotime($sched_end_1)) {
                continue;
            }

            if (strtotime($time_out = $item['time_out']) < strtotime($sched_end_1)) {
                $sched_end_1 = $time_out;
            }
    
            $hours += Carbon::parse($item['time_in'])
                ->floatDiffInHours($sched_end_1);
        }

        return $hours;

    }

    protected function secondQuarter()
    {
        $hours = 0;

        $sched_start_2 = $this->data['sched_start_2'];

        foreach ($this->mappedTimeLogs() as $key => $item) {

            if (is_null($item['time_in']) || is_null($item['time_out'])) {
                continue;
            }

            if (strtotime($item['time_out']) < strtotime($sched_start_2)) {
                continue;
            }
    
            if (strtotime($time_in = $item['time_in']) > strtotime($sched_start_2)) {
                $sched_start_2 = $time_in;
            }
    
            $hours += Carbon::parse($sched_start_2)
                ->floatDiffInHours($item['time_out']);
        }

        return $hours;

    }

    protected function mappedTimeLogs()
    {
        $timeLogs = collect($this->data['time_logs']);

        if ($timeLogs->isEmpty()) {
            return array();
        }

        return $timeLogs->map(function ($item, $key) use ($timeLogs) {

            if (key($timeLogs->toArray()) === $key) { //get lower-bound (Time In)
                $item['time_in'] = $this->isLate() ? $timeLogs->pluck('time_in')->first() : $this->data['sched_start_1'];
            }

            return $item;

        })->toArray();

    }

    protected function isLate()
    {
        $timeStart = Carbon::createFromTimeString($this->data['sched_start_1'])
                        ->addMinutes(self::LATE_ALLOWANCE);

        return strtotime(collect($this->data['time_logs'])->pluck('time_in')->first()) > strtotime($timeStart);
    }

}
