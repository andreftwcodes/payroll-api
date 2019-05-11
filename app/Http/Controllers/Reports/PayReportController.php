<?php

namespace App\Http\Controllers\Reports;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\PayReportResource;

class PayReportController extends Controller
{
    public function action(Request $request)
    {
        return PayReportResource::collection(
            Attendance::with(['employee.rate', 'employee.schedule'])
                ->where('employee_id', $request->employee_id)
                    ->get()
        );
    }
}
