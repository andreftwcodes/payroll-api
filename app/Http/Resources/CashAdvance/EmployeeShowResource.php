<?php

namespace App\Http\Resources\CashAdvance;

use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeShowResource extends JsonResource
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
            'fullname' => $this->getFullname()
        ];
    }
}
