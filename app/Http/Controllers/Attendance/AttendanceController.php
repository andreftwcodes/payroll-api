<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\TimeLogs;
use App\Models\Attendance;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\ScheduleTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Attendance\AttendanceResource;
use App\Http\Resources\Attendance\AttendanceEmployeeDropDownResource;

class AttendanceController extends Controller
{
    use ScheduleTrait; 
    
    public function index(Request $request)
    {
        $attendance = AttendanceResource::collection(
            Attendance::with(['employee', 'employee.payslips', 'locale', 'time_logs'])
                ->applyDateFilter($request)
                    ->get()
        );

        return $attendance->additional([
            'employees' => $this->getDropDownEmployees($request)
        ]);
    }

    public function store(Request $request)
    {

        if (!is_null($errors = $this->validateTimeLogs($request))) {
            return response()->json($errors, 422);
        }

        $employee = Employee::with('schedules', 'other')->find($request->employee_id);

        $attendanceData = [
            'locale_id'   => $request->locale_id,
            'amount'      => $employee->rate,
            'night_shift' => $employee->other->night_shift,
            'overtime'    => $employee->other->overtime,
            'attended_at' => $request->attended_at
        ];

        $mergedData = array_merge(
            $attendanceData,
            $this->mappedScheduleData($employee->schedules, $request->attended_at)
        );

        $attendance = $employee->attendance()->create($mergedData);
        
        $attendance->time_logs()
            ->createMany(
                collect($this->timeLogs($request))->toArray()
            );

        return new AttendanceResource(
            $attendance->load([
                'employee', 'locale', 'time_logs'
            ])
        );

    }

    public function update(Request $request, Attendance $attendance)
    {

        if (!is_null($errors = $this->validateTimeLogs($request))) {
            return response()->json($errors, 422);
        }

        $attendance->update(
            $request->only('locale_id')
        );

        $time_logs = $this->timeLogs($request);

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

    public function verifyEmployee(Request $request, Employee $employee)
    {
        if ($this->isClosed($employee, $request)) {
            return response()->json([
                'errors' => [
                    'employee' => ["This attendance is closed for {$employee->firstname} {$employee->middlename} {$employee->lastname}."]
                ]
            ], 422);
        }
    }

    private function getDropDownEmployees($request)
    {
        $with = [
            'schedules' => function ($query) use ($request) {
                $query->where([
                    array('day', Carbon::parse($request->attended_at)->format('N')),
                    array('status', 1)
                ]);
            },
            'locale'
        ];

        return AttendanceEmployeeDropDownResource::collection(
            Employee::with($with)
                ->active()
                ->whereNotIn('id', $this->getAttendedEmployeesByIds($request))
                ->get()
        );
    }

    private function getAttendedEmployeesByIds($request)
    {
        return Attendance::select('employee_id')
            ->whereDate('attended_at', $request->attended_at)
            ->get()
            ->toArray();
    }

    private function isClosed($employee, $request)
    {
        return $employee->payslips()->checkPeriod($request)->exists();
    }

    protected function timeLogs($request)
    {
        return collect($request->time_logs)->map(function ($item, $key) {
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

    private function validateTimeLogs($request)
    {
        $flag = null;
        $errors = null;

        foreach ($request->time_logs as $key => $item) {

            if (is_null($item['time_in']) && is_null($item['time_in'])) {
                $errors = [
                    'index'    => $key,
                    'time_in'  => ["The time in field is required."],
                    'time_out' => ["The time out field is required."]
                ];
                break;
            } elseif (is_null($item['time_in'])) {
                $errors = [
                    'index'    => $key,
                    'time_in'  => ["The time in field is required."]
                ];
                break;
            } elseif (is_null($item['time_out'])) {
                $errors = [
                    'index'    => $key,
                    'time_out' => ["The time out field is required."]
                ];
                break;
            }
                
            if (strtotime($item['time_in']) >= strtotime($time_out = $item['time_out'])) {
                $errors = [
                    'index'    => $key,
                    'time_in'  => ["Time in must be less than time out."],
                    'time_out' => ["Time out must be greater than time in."]
                ];
                break;
            } elseif (!is_null($flag) && strtotime($item['time_in']) <= strtotime($flag)) {
                $errors = [
                    'index'   => $key,
                    'time_in' => ["Time in must be greater than the previous time out."]
                ];
                break;
            }

            $flag = $time_out;

        }

        if (!is_null($errors)) {
            
            $errors = [
                'errors' => $errors
            ];

        }

        return $errors;
    }
}
