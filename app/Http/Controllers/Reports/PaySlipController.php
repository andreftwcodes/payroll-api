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

            $employee = Employee::with('locale')->find($request->employee_id);

            $payslipData = (new PaySlip($request, $employee))->getResult();

            $payslip = collect($payslipData['data'])->merge([
                'locale' => $employee->locale->name
            ]);

            $pdf = PDF::loadView('payslip.toPDF', compact('payslip'));
   
            return response($pdf->output(), 200)->withHeaders([
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename={$employee->lastname} - {$payslip['period']}.pdf",
            ]);

        }
    }

    public function getEmployees(Request $request)
    {
        return PaySlipEmployeeDataResource::collection(
            Employee::with(['other', 'ca_parent'])->applyFilter($request)->get()
        );
    }

    public function checkPeriod(Request $request, Employee $employee)
    {
        $payslip = $employee->payslips()->get()->first(function ($payslip, $key) use ($request) {
            return $payslip->payslip_periods()
                ->whereBetween('date', $request->only('from', 'to'))
                    ->exists();
        });

        $response = [
            'exists' => false
        ];
        
        if (!is_null($payslip)) {

            $period = collect($payslip->payslip_periods()->get());

            $response = [
                'exists' => true,
                'message' => "Overlapping a existing period between {$period->first()->date} and {$period->last()->date}."
            ];

        }

        return response()->json($response);
    }

    public function closePeriod(Request $request, Employee $employee)
    {
        $payslip = $employee->payslips()->create(
            $request->only('contributions')
        );

        $payslip->payslip_periods()->createMany(
            $this->dateRanges($request->from, $request->to)
        );

        if ($request->filled('ca_debit_amt')) {

            $employee->ca_parent->ca_children()->create([
                'payslip_id' => $payslip->id,
                'date'       => now()->format('Y-m-d'),
                'debit'      => $request->ca_debit_amt
            ]);

        }
    }

    private function dateRanges($start, $end) {

        $dates   = array();
        $current = strtotime($start);
        $end     = strtotime($end);
    
        while($current <= $end) {
            $dates[] = ['date' => date('Y-m-d', $current)];
            $current = strtotime('+1 day', $current);
        }
    
        return $dates;
    }
}
