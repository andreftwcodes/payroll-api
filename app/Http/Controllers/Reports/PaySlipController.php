<?php

namespace App\Http\Controllers\Reports;

use App\Libraries\Loan;
use App\Models\Employee;
use App\Libraries\PaySlip;
use Illuminate\Http\Request;
use App\Models\GovernmentLoan;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaySlip\PaySlipFlagResource;
use App\Http\Requests\PaySlip\PaySlipVerifyPeriodRequest;
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
        return PaySlipEmployeeDataResource::collection(
            Employee::applyFilter($request)->get()
        );
    }

    public function verifyPeriod(PaySlipVerifyPeriodRequest $request)
    {
        $employee = Employee::find($request->employee_id);

        $payslip = $employee->payslips()->checkPeriod($request);

        if ($payslip->exists()) {

            return response()->json([
                'errors' => [
                    'period' => [
                        "- Overlapping a existing period from {$payslip->first()->from} to {$payslip->first()->to}."
                    ]
                ]
            ], 422);

        }

        $eagerLoads = [
            'other',
            'ca_parent',
            'ca_parent.ca_children',
            'government_loans' => function ($query) {
                $query->has('government_loan_payments', '<', 24);
            }
        ];

        return new PaySlipFlagResource(
            $employee->load($eagerLoads)
        );

    }

    public function closePeriod(Request $request, Employee $employee)
    {
        $payslip = $employee->payslips()->create(
            $request->only('from', 'to', 'contributions')
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

        if ($request->filled('loan_id')) {

            if ($request->contributions) {

                GovernmentLoan::whereIn('id', $request->loan_id)->get()->each(function ($loan) use ($payslip) {
                    if (Loan::canDeduct($loan->loaned_at)) {
                        $loan->government_loan_payments()->create([
                            'payslip_id' => $payslip->id,
                            'paid_at'    => now()
                        ]);
                    }
                });

            }

        }

        $eagerLoads = [
            'other',
            'ca_parent',
            'ca_parent.ca_children',
            'government_loans' => function ($query) {
                $query->has('government_loan_payments', '<', 24);
            }
        ];

        return new PaySlipEmployeeDataResource(
            $employee->load($eagerLoads)
        );
    }
    
}
