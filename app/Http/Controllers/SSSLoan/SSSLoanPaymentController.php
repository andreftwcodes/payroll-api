<?php

namespace App\Http\Controllers\SSSLoan;

use App\Models\SSS_Loan;
use Illuminate\Http\Request;
use App\Models\SSSLoanPayment;
use App\Http\Controllers\Controller;

class SSSLoanPaymentController extends Controller
{
    public function store(Request $request, SSS_Loan $sss_loan)
    {
        $data = $request->only('paid_at');

        if ($request->filled('payslip_id')) {
            $data = [
                'payslip_id' => $request->payslip_id,
                'paid_at'    => now()
            ];
        }

        $payment = $sss_loan->sss_loan_payments()->create($data);
        dd($payment);
    }

    public function update(Request $request, SSSLoanPayment $sss_loan_payment)
    {
        $sss_loan_payment->update($request->only('paid_at'));

        dd($sss_loan_payment);
    }

    public function destroy(SSSLoanPayment $sss_loan_payment)
    {
        $sss_loan_payment->delete();
    }
}
