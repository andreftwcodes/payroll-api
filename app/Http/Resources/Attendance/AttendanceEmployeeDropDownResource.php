<?php

namespace App\Http\Resources\Attendance;

use App\Traits\EmployeeTrait;
use App\Traits\AttendanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceEmployeeDropDownResource extends JsonResource
{
    use EmployeeTrait, AttendanceTrait;
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
            'schedule_display' => $this->schedule_display(),
            'time_logs' => []
        ];
    }

    private function schedule_display()
    {
        if (!is_null($schedule = $this->schedules->first())) {
            return $this->getFormattedTime($schedule->start_1) . ' - ' . $this->getFormattedTime($schedule->end_2);
        }
    }
}
