<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaySlip\PaySlipRequest;
use App\Http\Requests\PaySlip\PayrollPeriodIndexRequest;

class Validator extends Controller
{
    public function deductionFilters(PaySlipRequest $request){}
    public function payrollPeriodsFilters(PayrollPeriodIndexRequest $request){}
}
