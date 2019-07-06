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
                ->whereDate('created_at', today())
                    ->update(
                        $this->getDataSet($request)
                    );
        }
    }

    protected function canUpdate($employee)
    {
        return $employee->attendance()->whereDate('created_at', today())->count();
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
            'sched_start_1' => $schedule['start_1'],
            'sched_end_1' => $schedule['end_1'],
            'sched_start_2' => $schedule['start_2'],
            'sched_end_2' => $schedule['end_2'],
        ];
    }

}
