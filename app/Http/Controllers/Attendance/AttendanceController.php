<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\TimeLogs;
use App\Models\Attendance;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\AttendanceRequest;
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
        if ($this->hasNoListsToday($request)) {

            if (!is_null($employees = $this->getEmployeesBatchData($request))) {
                Attendance::insert($employees);
            }

        }
    }

    public function update(AttendanceRequest $request, Attendance $attendance)
    {
        $attendance->update(
            $request->only('locale_id')
        );

        $time_logs = collect($request->time_logs);

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
                $this->mappedTimeLogs($toCreateData)
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

    protected function hasNoListsToday($request)
    {
        return !(bool) Attendance::whereDate('created_at', Carbon::parse($request->created_at)->toDateString())->count();
    }

    protected function mappedTimeLogs($timeLogs)
    {
        return $timeLogs->map(function ($timeLog, $key) {

            $time_out = null;

            if (!is_null($timeLog['time_out'])) {
                $time_out = Carbon::parse($timeLog['time_out'])->toDateTimeString();
            }

            $timeSet = [
                'time_in'  => Carbon::parse($timeLog['time_in'])->toDateTimeString(),
                'time_out' => $time_out
            ];

            if (collect($timeLog)->has('id')) {
                return Arr::add($timeSet, 'id', $timeLog['id']);
            }

            return $timeSet;
            
        })->toArray();
    }

    protected function getEmployeesBatchData($request)
    {
        $employees = Employee::with(['locale', 'rate', 'schedules', 'other'])->active()->get()->map(function ($item, $key) use ($request) {
            
            $employee = [
                'employee_id' => $item->id,
                'locale_id'   => $item->locale['id'],
                'amount'      => $item->rate['amount'],
                'night_shift'    => $item->other['night_shift'],
                'overtime'       => $item->other['overtime'],
                "created_at"     => $timestamps = Carbon::parse($request->created_at)->toDateTimeString(), 
                "updated_at"     => $timestamps
            ];

            return array_merge(
                $employee,
                $this->schedule($item->schedules, $request)
            );
            
        });

        if ($employees->isEmpty()) {
            return null;
        }

        return $employees->toArray();
    }

    protected function schedule($schedules, $request)
    {
        $schedule = collect($schedules)->first(function ($schedule, $key) use ($request) {
            return $schedule['day'] === (int) Carbon::parse($request->created_at)->format('N');
        });

        $start_1 = "{$request->created_at} {$schedule['start_1']}";
        $end_1   = "{$request->created_at} {$schedule['end_1']}";
        $start_2 = "{$request->created_at} {$schedule['start_2']}";
        $end_2   = "{$request->created_at} {$schedule['end_2']}";

        return [
            'sched_start_1'  => $start_1,
            'sched_end_1'    => $end_1,
            'sched_start_2'  => $start_2,
            'sched_end_2'    => $end_2
        ];
    }

    protected function updateTimeLogs($toUpdateData, $attendance)
    {
        $mapped = $this->mappedTimeLogs($toUpdateData);
        $id = $toUpdateData->pluck('id')->toArray();
        $timeLogs = $attendance->time_logs()->whereIn('id', $id)->get();
        $timeLogs->each(function($item, $key) use ($mapped) {
            if ($mapped[$key]['id'] === $item['id']) {
                $item['time_in']  = $mapped[$key]['time_in'];
                $item['time_out'] = $mapped[$key]['time_out'];
                return $item->save();
            }
        });
    }
}
