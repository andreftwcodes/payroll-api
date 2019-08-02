<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Rate\RateHistoryResource;

class EmployeeRateHistoryController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $employee->attachRateHistory($request);
    }

    public function show(Employee $employee)
    {
        return RateHistoryResource::collection(
            $employee->HistoryRateMappedData()
        );
    }
}
