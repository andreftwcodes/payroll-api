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
        $this->listAttendance();
        return AttendanceResource::collection(
            Attendance::with(['employee', 'locale', 'employee.schedule'])
                ->applyDateFilter($request)
                    ->get()
        );
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

    protected function listAttendance()
    {
        if ($this->hasNoListsToday()) {
            Employee::with(['locale', 'rate', 'schedule', 'other'])->active()->get()->each(function ($item, $key) {
                Attendance::create([
                    'employee_id' => $item->id,
                    'locale_id'   => $item->locale['id'],
                    'amount'      => $item->rate['amount'],
                    'sched_start' => $item->schedule['start'],
                    'sched_end'   => $item->schedule['end'],
                    'special_person' => $item->other['special_person'],
                    'night_shift'    => $item->other['night_shift'],
                    'overtime'       => $item->other['overtime'],
                ]);
            });
        }
    }

    protected function hasNoListsToday()
    {
        return !(bool) Attendance::whereDate('created_at', Carbon::today()->toDateString())->count();
    }
}
