<?php

namespace App\Http\Resources\Attendance;

use Carbon\Carbon;
use App\Traits\AttendanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attendance\AttendanceLocaleResource;
use App\Http\Resources\Attendance\AttendanceEmployeeResource;
use App\Http\Resources\Attendance\AttendanceTimeLogsResource;

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
            'id'        => $this->id,
            'employee'  => new AttendanceEmployeeResource($this->whenLoaded('employee')),
            'locale'    => new AttendanceLocaleResource($this->whenLoaded('locale')),
            'time_in'   => $this->getFormattedTime($this->timeIn()),
            'time_out'  => $this->getFormattedTime($this->timeOut()),
            'time_logs' => AttendanceTimeLogsResource::collection($this->whenLoaded('time_logs')),
            'remark'    => $this->getRemark(),
            'schedule_display' => $this->scheduleDisplay(),
            'status'    => $this->status()
        ];
    }

    protected function status()
    {
        return is_null($this->attendance_status);
    }

    protected function scheduleDisplay()
    {
        return "{$this->getFormattedTime($this->sched_start_1)} - {$this->getFormattedTime($this->sched_end_2)}";
    }

    protected function timeIn()
    {
        return $this->time_logs()->get()->pluck('time_in')->first();
    }

    protected function timeOut()
    {
        return $this->time_logs()->get()->pluck('time_out')->last();
    }

}
