<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeShowResource;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Http\Resources\Employee\EmployeeResource;

class EmployeeController extends Controller
{
    public function index()
    {
        return EmployeeResource::collection(
            Employee::all()
        );
    }

    public function store(Request $request, Employee $employee)
    {
        return new EmployeeResource(
            $employee->addEmployee($request)
        );
    }

    public function update(Request $request, Employee $employee)
    {
        return new EmployeeResource(
            $employee->updateEmployee($request)
        );
    }

    public function show(Employee $employee)
    {
        return new EmployeeShowResource(
            $employee->load([
                'rate', 'deductions', 'schedule', 'locale', 'other'
            ])
        );
    }
}
