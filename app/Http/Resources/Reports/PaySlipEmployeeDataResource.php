<?php

namespace App\Http\Resources\Reports;

use App\Traits\EmployeeTrait;
use App\Http\Resources\Reports\GovernmentLoanResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Reports\CashAdvanceResource;

class PaySlipEmployeeDataResource extends JsonResource
{
    use EmployeeTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'fullname' => $this->getFullname(),
            'flags' => [
                'contributions' => $this->contributions(),
                'cash_advance' => new CashAdvanceResource($this->whenLoaded('ca_parent')),
                'loans' => GovernmentLoanResource::collection(
                    $this->whenLoaded('government_loans')
                )
            ]
        ];
    }

    protected function contributions()
    {
        return (bool) $this->other->contributions;
    }

}
