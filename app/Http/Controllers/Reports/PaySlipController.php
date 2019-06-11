<?php

namespace App\Http\Controllers\Reports;

use App\Models\Employee;
use Barryvdh\DomPDF\Facade as PDF;
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

    public function viewToPDF($secret_key = null)
    {
        if (!is_null($secret_key)) {

            $request = new Request(
                collect(json_decode(base64_decode($secret_key)))->toArray()
            );

            $payslipData = (new PaySlip(
                $request,
                Employee::find($request->employee_id)
            ))->getResult();

            $payslip = $payslipData['data'];

            $pdf = PDF::loadView('payslip.toPDF', compact('payslip'));
   
            return response($pdf->output(), 200)->withHeaders([
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=payslip.pdf",
            ]);

        }
    }

    public function getEmployees()
    {
        return PaySlipEmployeeDataResource::collection(
            Employee::all()
        );
    }
}
