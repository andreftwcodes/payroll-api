<?php

namespace App\Http\Controllers\SSSLoan;

use App\Models\Employee;
use App\Models\SSS_Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SSSLoan\SSSLoanStoreRequest;
use App\Http\Resources\SSSLoan\SSSLoanShowResource;
use App\Http\Resources\SSSLoan\SSSLoanIndexResource;
use App\Http\Resources\SSSLoan\SSSLoanEmployeeResource;

class SSSLoanController extends Controller
{
    public function index()
    {
        $SSSLoanIndexResource = SSSLoanIndexResource::collection(
            SSS_Loan::with([
                'employee',
                'sss_loan_payments',
                'sss_loan_payments.sss_loan'
            ])->get()
        );

        $SSSLoanIndexResource->additional([
            'employees' => SSSLoanEmployeeResource::collection(
                Employee::active()->get()
            )
        ]);

        return $SSSLoanIndexResource;
    }

    public function store(SSSLoanStoreRequest $request)
    {
        $sss_loan = Employee::find($request->employee_id)->sss_loans()->create(
            $request->only('ref_no', 'amount_loaned', 'amortization_amount', 'loaned_at')
        );

        return new SSSLoanIndexResource(
            $sss_loan->load('employee', 'sss_loan_payments')
        );
    }

    public function show($id)
    {
        $load = [
            'employee',
            'sss_loan_payments' => function ($query) {
                $query->orderBy('paid_at', 'asc');
            }
        ];

        return new SSSLoanShowResource(
            SSS_Loan::with($load)->find($id)
        );
    }

    public function destroy($id)
    {
        SSS_Loan::find($id)->delete();
    }

    public function verify(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'subject'     => 'required'
        ]);

        $sss_loan = Employee::find($request->employee_id)->sss_loans()->has('sss_loan_payments', '<', 24);

        if (!is_null($item = $sss_loan->first())) {
            return response()->json([
                'errors' => [
                    'employee_id' => ["Has a remaining balance Ref No. {$item->ref_no}."],
                ]
            ], 422);
        }

    }

}
