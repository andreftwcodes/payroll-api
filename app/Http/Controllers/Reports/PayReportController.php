<?php

namespace App\Http\Controllers\Reports;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\GrossPayReportBaseResource;
use App\Http\Resources\Reports\EmployeeReportResource;

class PayReportController extends Controller
{
    public function employees()
    {
        return EmployeeReportResource::collection(
            Employee::all()
        );
    }

    public function pay(Request $request, Employee $employee)
    {   
        return new GrossPayReportBaseResource($employee);
    }

}
