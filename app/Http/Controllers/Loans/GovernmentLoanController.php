<?php

namespace App\Http\Controllers\Loans;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\GovernmentLoan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loans\GovernmentLoanStoreRequest;
use App\Http\Resources\Loans\GovernmentLoanShowResource;
use App\Http\Resources\Loans\GovernmentLoanIndexResource;
use App\Http\Resources\Loans\GovernmentLoanEmployeeResource;

class GovernmentLoanController extends Controller
{
    public function index()
    {
        $loans = GovernmentLoanIndexResource::collection(
            GovernmentLoan::with([
                'employee',
                'government_loan_payments',
                'government_loan_payments.government_loan'
            ])->get()
        );

        $loans->additional([
            'employees' => GovernmentLoanEmployeeResource::collection(
                Employee::active()->get()
            )
        ]);

        return $loans;
    }

    public function store(GovernmentLoanStoreRequest $request)
    {
        $loan = Employee::find($request->employee_id)->government_loans()->create(
            $request->only('ref_no', 'subject', 'amount_loaned', 'amortization', 'loaned_at')
        );

        return new GovernmentLoanIndexResource(
            $loan->load([
                'employee',
                'government_loan_payments',
                'government_loan_payments.government_loan'
            ])
        );
    }

    public function show($id)
    {
        $load = [
            'employee',
            'government_loan_payments' => function ($query) {
                $query->orderBy('paid_at', 'asc');
            }
        ];

        return new GovernmentLoanShowResource(
            GovernmentLoan::with($load)->find($id)
        );
    }

    public function destroy($id)
    {
        GovernmentLoan::find($id)->delete();
    }

    public function verify(Request $request)
    {
        $messages = [
            'employee_id.required' => 'The employee field is required.',
        ];

        $request->validate([
            'employee_id' => 'required',
            'subject'     => 'required'
        ], $messages);

        $loan = Employee::find($request->employee_id)
            ->government_loans()
            ->where('subject', $request->subject)
            ->has('government_loan_payments', '<', 24);

        if (!is_null($item = $loan->first())) {
            return response()->json([
                'errors' => [
                    'employee_id' => ["Has a remaining balance Ref No. {$item->ref_no}."],
                ]
            ], 422);
        }

    }

}
