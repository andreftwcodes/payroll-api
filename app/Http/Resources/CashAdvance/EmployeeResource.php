<?php

namespace App\Http\Resources\CashAdvance;

use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CashAdvance\CashAdvanceResource;

class EmployeeResource extends JsonResource
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
            'parent' => new CashAdvanceResource($this->whenLoaded('ca_parent'))
        ];
    }
}
