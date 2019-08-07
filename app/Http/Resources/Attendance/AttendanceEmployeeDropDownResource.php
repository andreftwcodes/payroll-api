<?php

namespace App\Http\Resources\Attendance;

use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceEmployeeDropDownResource extends JsonResource
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
            'employee' => [
                'id' => $this->id,
                'fullname' => $this->getFullname()
            ],
            'locale' => $this->locale->id,
            'schedule_display' => 'from - to',
            'time_logs' => []
        ];
    }
}
