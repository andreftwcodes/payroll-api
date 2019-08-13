<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateAttendanceAttributesController extends Controller
{
    public function update(Request $request, Employee $employee)
    {
        if ($this->canUpdate($employee)) {
            return $employee->attendance()
                ->whereDate('attended_at', today())
                    ->update(
                        $this->getDataSet($request)
                    );
        }
    }

    protected function canUpdate($employee)
    {
        return $employee->attendance()->whereDate('attended_at', today())->count();
    }

    protected function getDataSet($request)
    {
        $data = $request->only('amount', 'schedules', 'night_shift', 'overtime');
        return Arr::except(
            array_merge($data, $this->getSchedule($data['schedules'])),
            array('schedules')
        );
    }

    protected function getSchedule($schedules)
    {
        $schedule = collect($schedules)->first(function ($schedule, $key) {
            return $schedule['day'] === (int) Carbon::today()->format('N');
        });
        
        return [
            'sched_start_1' => Carbon::parse($schedule['start_1'])->toDateTimeString(),
            'sched_end_1'   => Carbon::parse($schedule['end_1'])->toDateTimeString(),
            'sched_start_2' => Carbon::parse($schedule['start_2'])->toDateTimeString(),
            'sched_end_2'   => Carbon::parse($schedule['end_2'])->toDateTimeString()
        ];
    }

}
