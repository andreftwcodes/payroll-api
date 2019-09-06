<?php

namespace App\Http\Controllers\Reports;

use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\Reports\TimeSheetResource;
use App\Http\Requests\Reports\GetTimeSheetRequest;
use App\Http\Resources\Reports\TimeSheetEmployeeResource;

class TimeSheetController extends Controller
{
    public function index()
    {
        return TimeSheetEmployeeResource::collection(
            Employee::all()
        );
    }

    public function getTimeSheet(GetTimeSheetRequest $request)
    {
        $with = [
            'attendances' => function ($query) use ($request) {
                $query->whereBetween('attended_at', [
                    $request->from,
                    $request->to
                ]);
                $query->orderBy('attended_at', 'asc');
            },
            'attendances.locale:id,name'
        ];

        return new TimeSheetResource(
            Employee::with($with)->find($request->employee_id)
        );
    }

    public function viewToPDF($secret_key = null)
    {
        if (!is_null($secret_key)) {

            $request = new GetTimeSheetRequest(
                collect(json_decode(base64_decode($secret_key)))->toArray()
            );

            $timesheet = new TimeSheetResource(
                $this->mappedEmployee(
                    Employee::with(['attendances'])->find($request->employee_id),
                    $request
                )
            );

            $timesheet = collect($timesheet)->toArray();

            dd($timesheet);

            $pdf = PDF::loadView('timesheet.toPDF', compact('timesheet'));
   
            return response($pdf->output(), 200)->withHeaders([
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename={$timesheet['filename']}.pdf",
            ]);

        }
    }

    private function mappedEmployee($employee, $request)
    {
        return $employee->setAttribute('period', [
            $request->from,
            $request->to
        ]);
    }

    
}
