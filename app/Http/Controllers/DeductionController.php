<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use Illuminate\Http\Request;
use App\Http\Resources\Deduction\DeductionResource;

class DeductionController extends Controller
{
    public function index()
    {
        return DeductionResource::collection(
            Deduction::all()
        );
    }

    public function store(Request $request, Deduction $deduction)
    {
        return new DeductionResource(
            $deduction->createDeduction($request)
        );
    }

    public function update(Request $request, Deduction $deduction)
    {
        return new DeductionResource(
            $deduction->updateDeduction($request)
        );
    }

    public function show(Deduction $deduction)
    {
        return new DeductionResource($deduction);
    }
}
