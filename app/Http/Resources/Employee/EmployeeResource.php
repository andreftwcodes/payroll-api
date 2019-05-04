<?php

namespace App\Http\Resources\Employee;

use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'lastname' => $this->lastname,
            'gender' => $this->gender,
            'age' => $this->getAge(),
            'contact' => $this->contact,
            'birthdate' => $this->getBirthDate(),
            'status' => $this->status,
        ];
    }

}
