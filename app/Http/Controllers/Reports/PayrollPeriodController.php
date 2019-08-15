<?php

namespace App\Http\Controllers\Reports;

use App\Models\PaySlip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollPeriods\PayrollPeriodIndexResource;

class PayrollPeriodController extends Controller
{
    public function index()
    {
        $with = [
            'employee',
            'ca_children',
            'sss_loan_payment',
            'payslip_periods'
        ];

        return PayrollPeriodIndexResource::collection(
            PaySlip::with($with)->get()
        );
    }

    public function destroy($id)
    {
        PaySlip::find($id)->delete();
    }
}
