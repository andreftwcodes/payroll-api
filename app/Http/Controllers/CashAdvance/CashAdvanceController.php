<?php

namespace App\Http\Controllers\CashAdvance;

use App\Models\Employee;
use App\Models\CA_PARENT;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CashAdvance\EmployeeResource;
use App\Http\Resources\CashAdvance\CashAdvanceShowResource;

class CashAdvanceController extends Controller
{
    public function index()
    {
        return EmployeeResource::collection(
            Employee::with(['ca_parent.ca_children'])->get()
        );
    }

    public function show(Employee $employee)
    {
        $parent = $employee->ca_parent();

        if ($parent->doesntExist()) {
            $parent->create();
        }

        return new CashAdvanceShowResource(
            $employee->ca_parent->load(['employee', 'ca_children'])
        );
    }

    public function store(Request $request)
    {
        $parent = CA_PARENT::find($request->ca_parents_id)->first();
        
        $children = $parent->ca_children()->create(
            $request->only('date', 'credit', 'debit')
        );

        dd($children);
    }
}
