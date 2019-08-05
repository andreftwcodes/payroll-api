<?php

namespace App\Http\Resources;

use App\Traits\EmployeeTrait;
use App\Http\Resources\Other\OtherResource;
use App\Http\Resources\Locale\LocaleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Employee\EmployeeScheduleResource;

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
            'profile' => [
                'id' => $this->id,
                'firstname' => $this->firstname,
                'middlename' => $this->middlename,
                'lastname' => $this->lastname,
                'gender' => $this->gender,
                'age' => $this->getAge(),
                'contact' => $this->contact,
                'birthdate' => $this->getBirthDate(),
                'status' => $this->status,
            ],
            'extras' => [
                'rate' => $this->getRate(),
                'payment_period' => $this->payment_period,
                'schedules' => EmployeeScheduleResource::collection($this->whenLoaded('schedules')),
                'locale' => new LocaleResource($this->whenLoaded('locale')),
                'other' => new OtherResource($this->whenLoaded('other'))
            ]
        ];
    }
}
