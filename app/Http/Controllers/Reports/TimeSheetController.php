<?php

namespace App\Http\Controllers\Reports;

use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
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
            'attendances.locale:id,name',
            'attendances.time_logs'
        ];

        return new TimeSheetResource(
            Employee::with($with)->find($request->employee_id)->setAttribute('period', [
                $request->from,
                $request->to
            ])
        );
    }

    public function viewToPDF($secret_key = null)
    {
        if (!is_null($secret_key)) {

            $request = new GetTimeSheetRequest(
                collect(json_decode(base64_decode($secret_key)))->toArray()
            );

            $timesheet = $this->getTimeSheet($request)
                ->response()
                    ->header('X-Value', 'True')
                        ->getData()
                            ->data;

            $pdf = PDF::loadView('timesheet.toPDF', compact('timesheet'))->setPaper('a4', 'landscape');
   
            return response($pdf->output(), 200)->withHeaders([
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename={$timesheet->filename}.pdf",
            ]);

        }
    }
    
}
