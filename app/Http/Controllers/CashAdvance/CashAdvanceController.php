<?php

namespace App\Http\Controllers\CashAdvance;

use App\Models\Employee;
use App\Models\CA_PARENT;
use Illuminate\Http\Request;
use App\Traits\CashAdvanceTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\CashAdvance\EmployeeResource;
use App\Http\Resources\CashAdvance\CashAdvanceShowResource;

class CashAdvanceController extends Controller
{
    use CashAdvanceTrait;
    
    public function index()
    {
        return EmployeeResource::collection(
            Employee::with(['ca_parent.ca_children'])->get()
        );
    }

    public function show(Employee $employee)
    {
        $load = [
            'employee',
            'ca_children' => function ($query) {
                $query->orderBy('date', 'asc');
            }
        ];

        return new CashAdvanceShowResource(
            $employee->ca_parent->load($load)
        );
    }

    public function store(Request $request)
    {
        $parent = CA_PARENT::with('ca_children')->find($request->ca_parents_id);

        $parent->ca_children()->create(
            $request->only('date', 'credit', 'debit')
        );

        return response()->json([
            'data' => $this->childrenMapper(
                $parent->ca_children()->orderBy('date', 'asc')->get()
            )
        ]);
    }

    public function attachLedger(Employee $employee)
    {
        $parent = $employee->ca_parent();

        if ($parent->doesntExist()) {
            $parent->create();
        }
    }

    public function amount_deductible(Request $request, CA_PARENT $ca_parent)
    {
        $ca_parent->update(
            $request->only('amount_deductible')
        );
    }
}
