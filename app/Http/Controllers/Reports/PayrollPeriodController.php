<?php

namespace App\Http\Controllers\Reports;

use App\Models\PaySlip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollPeriods\PayrollPeriodIndexResource;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $with = [
            'employee',
            'employee.locale:id,name',
            'ca_children',
            'government_loan_payments'
        ];

        return PayrollPeriodIndexResource::collection(
            PaySlip::with($with)->applyFilters($request)->latest()->take(50)->get()
        );
    }

    public function destroy($id)
    {
        PaySlip::find($id)->delete();
    }
}
