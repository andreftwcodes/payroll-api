<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\TimeLogs;
use App\Models\Attendance;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Attendance\AttendanceResource;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        return AttendanceResource::collection(
            Attendance::with(['employee', 'locale', 'time_logs'])
                ->applyDateFilter($request)
                    ->get()
        );
    }

    public function store(Request $request)
    {
        if ($this->hasNotPersisted($request)) {
            if (!is_null($employees = $this->getEmployeesBatchData($request))) {
                Attendance::insert($employees);
            }
        }
    }

    public function update(Request $request, Attendance $attendance)
    {
        $attendance->update(
            $request->only('locale_id')
        );

        $time_logs = $this->timeLogsMapped(
            $request,
            $attendance
        );

        $toCreateData = $time_logs->filter(function ($item, $key) {
            return !collect($item)->has('id');
        });

        $toUpdateData = $time_logs->filter(function ($item, $key) {
            return collect($item)->has('id');
        });

        $toDeleteData = $attendance->time_logs()->get()->filter(function ($item, $key) use ($toUpdateData) {
            return !$toUpdateData->contains('id', $item['id']);
        });

        if ($toCreateData->isNotEmpty()) {
            $attendance->time_logs()->createMany(
                $toCreateData->toArray()
            );
        }

        if ($toUpdateData->isNotEmpty()) {
            $this->updateTimeLogs($toUpdateData, $attendance);
        }

        if ($toDeleteData->isNotEmpty()) {
            TimeLogs::destroy(
                $toDeleteData->pluck('id')->toArray()
            );
        }

        return new AttendanceResource(
            $attendance->load([
                'employee', 'locale', 'time_logs'
            ])
        );
    }

    protected function hasNotPersisted($request)
    {
        $created_at = Carbon::parse($request->created_at)->toDateString();
        return Attendance::whereDate('created_at', $created_at)->doesntExist();
    }

    protected function getEmployeesBatchData($request)
    {
        $employees = Employee::with(['locale', 'schedules', 'other'])->active()->get()->map(function ($item, $key) use ($request) {
            
            if (is_null($schedule = $this->schedule($item->schedules, $request))) {
                return null;
            }

            $employee = [
                'employee_id' => $item->id,
                'locale_id'   => $item->locale['id'],
                'amount'      => $item->rate,
                'night_shift' => $item->other['night_shift'],
                'overtime'    => $item->other['overtime'],
                "created_at"  => $timestamps = Carbon::parse($request->created_at)->toDateTimeString(), 
                "updated_at"  => $timestamps
            ];

            return array_merge($employee, $schedule);
            
        });

        $employees = $employees->filter(function ($item) {
            return !is_null($item);
        });

        if ($employees->isEmpty()) {
            return null;
        }

        return $employees->toArray();
    }

    protected function timeLogsMapped($request, $attendance)
    {
        return collect($request->time_logs)->map(function ($item, $key) use ($attendance) {
            $date = Carbon::parse($attendance->created_at)->toDateString();
            $item['time_in']  = !is_null($item['time_in']) ? "{$date} {$item['time_in']}" : null;
            $item['time_out'] = !is_null($item['time_out']) ? "{$date} {$item['time_out']}" : null;
            return collect($item)->has('id') ? Arr::add($item, 'id', $item['id']) : $item;
        });
    }

    protected function updateTimeLogs($toUpdateData, $attendance)
    {
        $dataSet = $toUpdateData->toArray();
        $id = $toUpdateData->pluck('id')->toArray();
        $attendance->time_logs()->whereIn('id', $id)->get()->each(function($item, $key) use ($dataSet) {
            if ($dataSet[$key]['id'] === $item['id']) {
                $item['time_in']  = $dataSet[$key]['time_in'];
                $item['time_out'] = $dataSet[$key]['time_out'];
                return $item->save();
            }
        });
    }

    protected function schedule($schedules, $request)
    {
        $schedule = collect($schedules)->first(function ($schedule) use ($request) {
            return ((bool) $schedule['status']) && $schedule['day'] === (int) Carbon::parse($request->created_at)->format('N');
        });

        if (is_null($schedule)) {
            return null;
        }

        return [
            'sched_start_1'  => "{$request->created_at} {$schedule['start_1']}",
            'sched_end_1'    => "{$request->created_at} {$schedule['end_1']}",
            'sched_start_2'  => "{$request->created_at} {$schedule['start_2']}",
            'sched_end_2'    => "{$request->created_at} {$schedule['end_2']}"
        ];
    }
}
