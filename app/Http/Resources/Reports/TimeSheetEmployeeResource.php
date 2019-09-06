<?php

namespace App\Http\Resources\Reports;

use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeSheetEmployeeResource extends JsonResource
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
            'fullname' => $this->getFullname()
        ];
    }
}
