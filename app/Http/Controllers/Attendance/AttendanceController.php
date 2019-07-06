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
            Attendance::with(['employee', 'locale'])
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
        
        return new AttendanceResource(
            $attendance->load([
                'employee', 'locale'
            ])
        );
    }

    protected function hasNoListsToday()
    {
        return !(bool) Attendance::whereDate('created_at', Carbon::today()->toDateString())->count();
    }

    protected function getEmployeesBatchData()
    {
        $employees = Employee::with(['locale', 'rate', 'schedules', 'other'])->active()->get()->map(function ($item, $key) {
            
            $employee = [
                'employee_id' => $item->id,
                'locale_id'   => $item->locale['id'],
                'amount'      => $item->rate['amount'],
                'night_shift'    => $item->other['night_shift'],
                'overtime'       => $item->other['overtime'],
                "created_at"     => $timestamps = Carbon::now(), 
                "updated_at"     => $timestamps
            ];

            return array_merge(
                $employee,
                $this->schedule($item->schedules)
            );
            
        });

        if ($employees->isEmpty()) {
            return null;
        }

        return $employees->toArray();
    }

    protected function schedule($schedules)
    {
        $schedule = collect($schedules)->first(function ($schedule, $key) {
            return $schedule['day'] === (int) Carbon::today()->format('N');
        });

        return [
            'sched_start_1'  => $schedule['start_1'],
            'sched_end_1'    => $schedule['end_1'],
            'sched_start_2'  => $schedule['start_2'],
            'sched_end_2'    => $schedule['end_2']
        ];
    }
}
