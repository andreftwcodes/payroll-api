<?php

namespace App\Http\Controllers\Reports;

use App\Models\Employee;
use App\Models\SSS_Loan;
use App\Libraries\PaySlip;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
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

            $employee = Employee::find($request->employee_id);

            $payslipData = (new PaySlip($request, $employee))->getResult();

            $payslip = collect($payslipData['data']);

            $pdf = PDF::loadView('payslip.toPDF', compact('payslip'));
   
            return response($pdf->output(), 200)->withHeaders([
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename={$employee->lastname} - {$payslip['period']}.pdf",
            ]);

        }
    }

    public function getEmployees(Request $request)
    {
        $eagerLoads = [
            'other',
            'ca_parent',
            'ca_parent.ca_children',
            'sss_loans' => function ($query) {
                $query->has('sss_loan_payments', '<', 24);
            }
        ];

        return PaySlipEmployeeDataResource::collection(
            Employee::with($eagerLoads)->applyFilter($request)->get()
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

        if ($request->filled('ca_amount_deductible')) {

            if (($debit = $request->ca_amount_deductible) !== 0) {

                $employee->ca_parent->ca_children()->create([
                    'payslip_id' => $payslip->id,
                    'date'       => now(),
                    'debit'      => $debit
                ]);

            }

        }

        if ($request->filled('sss_loan_id')) {

            if ($request->contributions) {

                SSS_Loan::find($request->sss_loan_id)->sss_loan_payments()->create([
                    'payslip_id' => $payslip->id,
                    'paid_at'    => now()
                ]);

            }

        }

        $payslip->attendance_statuses()->createMany(
            $this->getAttendanceIds($request, $employee)
        );

        $eagerLoads = [
            'other',
            'ca_parent',
            'ca_parent.ca_children',
            'sss_loans'
        ];

        return new PaySlipEmployeeDataResource(
            $employee->load($eagerLoads)
        );
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

    private function getAttendanceIds($request, $employee)
    {
        $items = $employee->attendances()
            ->select('id as attendance_id')
                ->whereBetween('attended_at', $request->only('from', 'to'))
                    ->get();

        return $items->toArray();
    }
}
