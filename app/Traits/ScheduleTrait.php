<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait ScheduleTrait
{
    protected function mappedScheduleData($schedules, $attended_at = null)
    {

        $schedule = $this->getSchedule(
            $schedules,
            $attended_at = !is_null($attended_at) ? $attended_at : today()->toDateString()
        );

        $set = [
            'sched_start_1' => "{$attended_at} {$schedule['start_1']}",
            'sched_end_1'   => "{$attended_at} {$schedule['end_1']}",
            'sched_start_2' => "{$attended_at} {$schedule['start_2']}",
            'sched_end_2'   => "{$attended_at} {$schedule['end_2']}"
        ];

        if (strtotime($start_1 = $set['sched_start_1']) > strtotime($end_1 = $set['sched_end_1'])) {
            $set['sched_end_1'] = Carbon::parse($end_1)->addDay()->toDateTimeString();
        }

        if (strtotime($start_1) > strtotime($start_2 = $set['sched_start_2'])) {
            $set['sched_start_2'] = Carbon::parse($start_2)->addDay()->toDateTimeString();
        }

        if (strtotime($start_1) > strtotime($end_2 = $set['sched_end_2'])) {
            $set['sched_end_2'] = Carbon::parse($end_2)->addDay()->toDateTimeString();
        }

        return $set;

    }


    private function getSchedule($schedules, $attended_at)
    {
        return collect($schedules)->first(function ($schedule) use ($attended_at) {
            return $schedule['day'] === (int) Carbon::parse($attended_at)->format('N');
        });
    }
}
