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
        return SSSLoanIndexResource::collection(
            SSS_Loan::with('employee', 'sss_loan_payments')->get()
        );
    }

    public function store(SSSLoanStoreRequest $request, Employee $employee)
    {
        $sss_loan = $employee->sss_loans()->create($request->only('loan_no', 'amount', 'loaned_at'));
        dd($sss_loan);
    }

    public function update(SSSLoanUpdateRequest $request, SSS_Loan $sss_loan)
    {
        $sss_loan->update($request->only('loan_no', 'amount', 'loaned_at'));
        dd($sss_loan);
    }

    public function show(SSS_Loan $sss_loan)
    {
        $load = [
            'employee',
            'sss_loan_payments' => function ($query) {
                $query->orderBy('paid_at', 'asc');
            }
        ];

        return new SSSLoanShowResource(
            $sss_loan->load($load)
        );
    }

    public function destroy(SSS_Loan $sss_loan)
    {
        $sss_loan->delete();
    }

    public function getEmployees()
    {
        return SSSLoanEmployeeResource::collection(
            Employee::active()->get()
        );
    }
}
