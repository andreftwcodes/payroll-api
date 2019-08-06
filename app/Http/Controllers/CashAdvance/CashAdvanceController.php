<?php

namespace App\Http\Controllers\CashAdvance;

use App\Models\Employee;
use App\Models\CA_PARENT;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CashAdvance\EmployeeResource;
use App\Http\Resources\CashAdvance\CashAdvanceShowResource;
use App\Http\Resources\CashAdvance\CashAdvanceChildrenResource;

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
        $parent = CA_PARENT::find($request->ca_parents_id);

        $children = $parent->ca_children()->create(
            $request->only('date', 'credit', 'debit')
        );

        return (new CashAdvanceChildrenResource($children))
            ->additional([
                'meta' => [
                    'balance' => $this->getBalance($parent)
                ]
            ]);
    }

    public function amount_deductible(Request $request, CA_PARENT $ca_parent)
    {
        $ca_parent->update(
            $request->only('amount_deductible')
        );
    }

    private function getBalance($parent)
    {
        $childrens = collect($parent->ca_children()->get());
        return number_format($childrens->sum('credit') - $childrens->sum('debit'), 2);
    }
}
