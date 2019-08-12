<?php

namespace App\Http\Resources\CashAdvance;

use App\Traits\CashAdvanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CashAdvance\EmployeeShowResource;
use App\Http\Resources\CashAdvance\CashAdvanceChildrenResource;

class CashAdvanceShowResource extends JsonResource
{
    use CashAdvanceTrait;
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
            'employee' => new EmployeeShowResource(
                $this->whenLoaded('employee')
            ),
            'amount_deductible' => $this->amount_deductible,
            'children' => $this->childrenMapper(
                CashAdvanceChildrenResource::collection(
                    $this->whenLoaded('ca_children')
                )
            )
        ];
    }
}
