<?php

namespace App\Http\Resources\PaySlip;

use App\Traits\PaySlipTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Reports\CashAdvanceResource;
use App\Http\Resources\Reports\GovernmentLoanResource;

class PaySlipFlagResource extends JsonResource
{
    use PaySlipTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'employee_id'   => $this->id,
            'contributions' => $this->contributions(),
            'cash_advance'  => new CashAdvanceResource($this->whenLoaded('ca_parent')),
            'loans'         => GovernmentLoanResource::collection($this->whenLoaded('government_loans'))
        ];
    }

    private function contributions()
    {
        return [
            'disabled' => $truthy = $this->other->contributions == 1 && $this->periodHasEndOfMonthDate(),
            'checked'  => $truthy
        ];
    }
}
