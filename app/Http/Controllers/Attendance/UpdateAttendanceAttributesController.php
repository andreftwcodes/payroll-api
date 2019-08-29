<?php

namespace App\Http\Controllers\Attendance;

use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\ScheduleTrait;
use App\Http\Controllers\Controller;

class UpdateAttendanceAttributesController extends Controller
{
    use ScheduleTrait;
    
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
            array_merge($data, $this->mappedScheduleData($data['schedules'])),
            array('schedules')
        );
    }

}
