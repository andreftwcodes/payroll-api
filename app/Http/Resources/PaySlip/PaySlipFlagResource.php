<?php

namespace App\Http\Resources\PaySlip;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Reports\CashAdvanceResource;
use App\Http\Resources\Reports\GovernmentLoanResource;

class PaySlipFlagResource extends JsonResource
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
            'employee_id' => $this->id,
            'contributions' => $this->contributions(),
            'cash_advance' => new CashAdvanceResource($this->whenLoaded('ca_parent')),
            'loans' => GovernmentLoanResource::collection(
                $this->whenLoaded('government_loans')
            )
        ];
    }

    protected function contributions()
    {
        return (bool) $this->other->contributions;
    }
}
