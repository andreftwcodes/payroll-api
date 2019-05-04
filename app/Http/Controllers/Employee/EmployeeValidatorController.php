<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Http\Requests\Employee\EmployeeExtrasRequest;

class EmployeeValidatorController extends Controller
{
    public function personal(EmployeeRequest $request){}
        
    public function employment(EmployeeExtrasRequest $request){}
}
