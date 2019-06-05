<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\AttendanceRequest;
use App\Http\Resources\Attendance\AttendanceResource;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        return AttendanceResource::collection(
            Attendance::with(['employee', 'locale', 'employee.schedule'])
                ->applyDateFilter($request)
                    ->get()
        );
    }

    public function store(Request $request)
    {
        if ($this->hasNoListsToday()) {

            if (!is_null($employees = $this->getEmployeesBatchData())) {
                Attendance::insert($employees);
            }

        }
    }

    public function update(AttendanceRequest $request, Attendance $attendance)
    {
        $attendance->update(
            $request->only('locale_id', 'start', 'end')
        );
        
        return new AttendanceResource($attendance->load([
            'employee', 'locale', 'employee.schedule'
        ]));
    }

    protected function hasNoListsToday()
    {
        return !(bool) Attendance::whereDate('created_at', Carbon::today()->toDateString())->count();
    }

    protected function getEmployeesBatchData()
    {
        $employees = Employee::with(['locale', 'rate', 'schedule', 'other'])->active()->get()->map(function ($item, $key) {
            return [
                'employee_id' => $item->id,
                'locale_id'   => $item->locale['id'],
                'amount'      => $item->rate['amount'],
                'sched_start_1'  => $item->schedule['start_1'],
                'sched_end_1'    => $item->schedule['end_1'],
                'sched_start_2'  => $item->schedule['start_2'],
                'sched_end_2'    => $item->schedule['end_2'],
                'special_person' => $item->other['special_person'],
                'night_shift'    => $item->other['night_shift'],
                'overtime'       => $item->other['overtime'],
                "created_at"     => $timestamps = Carbon::now(), 
                "updated_at"     => $timestamps
            ];
        });

        if ($employees->isEmpty()) {
            return null;
        }

        return $employees->toArray();
    }
}
