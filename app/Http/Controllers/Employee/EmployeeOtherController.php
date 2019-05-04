<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeOtherController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $employee->attachOther($request);
    }
}
