<?php

namespace App\Http\Controllers\Reports;

use App\Models\Employee;
use App\Libraries\PaySlip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\PaySlipEmployeeDataResource;

class PaySlipController extends Controller
{
    public function index()
    {

    }

    public function getPeriod(Request $request, Employee $employee)
    {
        $payslip = new PaySlip($request, $employee);

        return $payslip->getResult();
    }

    public function getEmployees()
    {
        return PaySlipEmployeeDataResource::collection(
            Employee::all()
        );
    }
}
