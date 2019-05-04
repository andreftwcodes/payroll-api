<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasDeductionTrait
{
    public function attachDeduction(Request $request)
    {
        $this->deductions()->sync($request->deductions);
    }
}
