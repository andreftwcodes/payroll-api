<?php

namespace App\Http\Controllers\SSSLoan;

use App\Models\Employee;
use App\Models\SSS_Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SSSLoan\SSSLoanStoreRequest;
use App\Http\Requests\SSSLoan\SSSLoanUpdateRequest;
use App\Http\Resources\SSSLoan\SSSLoanShowResource;
use App\Http\Resources\SSSLoan\SSSLoanIndexResource;
use App\Http\Resources\SSSLoan\SSSLoanEmployeeResource;

class SSSLoanController extends Controller
{
    public function index()
    {
        return (SSSLoanIndexResource::collection(
            SSS_Loan::with('employee', 'sss_loan_payments')->get()
        ))->additional([
            'employees' => SSSLoanEmployeeResource::collection(
                Employee::active()->get()
            )
        ]);
    }

    public function store(SSSLoanStoreRequest $request)
    {
        $sss_loan = Employee::find($request->employee_id)->sss_loans()->create(
            $request->only('loan_no', 'amount_loaned', 'amortization_amount', 'payment_terms', 'date_loaned')
        );

        return new SSSLoanIndexResource(
            $sss_loan->load('employee', 'sss_loan_payments')
        );
    }

    public function update(SSSLoanUpdateRequest $request, $id)
    {
        $sss_loan = SSS_Loan::find($id);

        $sss_loan->update(
            $request->only('loan_no', 'amount_loaned', 'amortization_amount', 'payment_terms', 'date_loaned')
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

    public function destroy()
    {
        //
    }
}
