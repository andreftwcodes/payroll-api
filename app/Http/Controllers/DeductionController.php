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
}
