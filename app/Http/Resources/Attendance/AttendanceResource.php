<?php

namespace App\Http\Resources\Attendance;

use Carbon\Carbon;
use App\Traits\AttendanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attendance\AttendanceLocaleResource;
use App\Http\Resources\Attendance\AttendanceEmployeeResource;

class AttendanceResource extends JsonResource
{
    use AttendanceTrait;
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
            'employee' => new AttendanceEmployeeResource($this->whenLoaded('employee')),
            'locale' => new AttendanceLocaleResource($this->whenLoaded('locale')),
            'start_dsp' => $this->getFormattedTime($this->start),
            'end_dsp' => $this->getFormattedTime($this->end),
            'start' => $this->start,
            'end' => $this->end,
            'remark' => $this->getRemark()
        ];
    }

}
