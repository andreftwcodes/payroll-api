<?php

namespace App\Http\Controllers\CashAdvance;

use App\Models\Employee;
use App\Models\CA_PARENT;
use App\Models\CA_CHILDREN;
use Illuminate\Http\Request;
use App\Traits\CashAdvanceTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\CashAdvance\EmployeeResource;
use App\Http\Requests\CashAdvance\CashAdvanceStoreRequest;
use App\Http\Resources\CashAdvance\CashAdvanceShowResource;
use App\Http\Resources\CashAdvance\CashAdvanceChildrenResource;

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
                $query->orderByDateAsc();
            }
        ];

        return new CashAdvanceShowResource(
            $employee->ca_parent->load($load)
        );
    }

    public function store(CashAdvanceStoreRequest $request)
    {

        if ($request->filled('credit') && $request->filled('debit')) {
            return response()->json([
                'errors' => [
                    'credit' => $message = ['Can not persist at the same time.'],
                    'debit'  => $message
                ]
            ], 422);
        }

        $parent = CA_PARENT::with('ca_children')->find($request->ca_parents_id);

        $parent->ca_children()->create(
            $request->only('date', 'credit', 'debit')
        );

        return response()->json([
            'data' => $this->childrenMapper(
                CashAdvanceChildrenResource::collection(
                    $parent->ca_children()->orderByDateAsc()->get()
                )
            )
        ]);
    }

    public function update(CashAdvanceStoreRequest $request, CA_CHILDREN $child)
    {
        if ($request->filled('credit') && $request->filled('debit')) {
            return response()->json([
                'errors' => [
                    'credit' => $message = ['Can not persist at the same time.'],
                    'debit'  => $message
                ]
            ], 422);
        }

        $child->update($request->only('date', 'credit', 'debit'));

        return response()->json([
            'data' => $this->childrenMapper(
                CashAdvanceChildrenResource::collection(
                    $child->ca_parent->ca_children()->orderByDateAsc()->get()
                )
            )
        ]);

    }

    public function destroy(CA_CHILDREN $child)
    {
        $child->delete();

        return response()->json([
            'data' => $this->childrenMapper(
                CashAdvanceChildrenResource::collection(
                    $child->ca_parent->ca_children()->orderByDateAsc()->get()
                )
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
