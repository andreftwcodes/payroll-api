<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeStatusController extends Controller
{
    public function update(Request $request, Employee $employee)
    {
        $employee->update($request->only('status'));
    }
}
