<?php

namespace App\Http\Resources\Reports;

use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

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
                'contributions' => $this->whenLoaded('deductions')->contains('id', 1)
            ]
        ];
    }
}
