<?php

namespace App\Http\Resources\PayrollPeriods;

use Illuminate\Http\Resources\Json\JsonResource;

class CashAdvanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'deductible_amount' => $this->debit
        ];
    }
}
