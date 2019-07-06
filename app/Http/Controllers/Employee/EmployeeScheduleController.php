<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeScheduleController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        if ($this->alreadyExist($employee)) {
            $this->updateData($employee, $request);
            return;
        }
        
        $employee->schedules()->createMany($request->schedules);
    }

    private function alreadyExist($employee)
    {
        return (bool) $employee->schedules()->count();
    }

    protected function updateData($employee, $request)
    {
        $id = $employee->schedules()->pluck('id')->toArray();
        $employees = $employee->schedules()->whereIn('id', $id)->get();
        $employees->each(function($item, $key) use ($request){
            $item['start_1'] = $request->schedules[$key]['start_1'];
            $item['end_1']   = $request->schedules[$key]['end_1'];
            $item['start_2'] = $request->schedules[$key]['start_2'];
            $item['end_2']   = $request->schedules[$key]['end_2'];
            $item['status']  = $request->schedules[$key]['status'];
            return $item->save();
        });
    }
}
