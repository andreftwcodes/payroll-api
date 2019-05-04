<?php

namespace App\Http\Resources\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attendance\AttendanceLocaleResource;
use App\Http\Resources\Attendance\AttendanceEmployeeResource;

class AttendanceResource extends JsonResource
{
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
            'start' => $this->start,
            'end' => $this->end,
            'remark' => $this->getRemark()
        ];
    }

    protected function getRemark()
    {
        $remark = 'On time';

        if (is_null($this->start)) {
           return 'Absent';
        }

        if ($this->getExceedTime() > $this->getLateAllowance()) {
            $remark = 'Late';
        }

        return $remark;
    }

    protected function getExceedTime()
    {
        $parsedStart = Carbon::parse(
            $start = $this->start
        );

        $parsedScheduleStart = Carbon::parse(
            $scheduleStart = $this->employee->schedule->start
        );

        if ($parsedScheduleStart->greaterThan($parsedStart)) {
            return 0;
        }

        return Carbon::parse($start)
                ->diffInMinutes($scheduleStart);
    }

    protected function getLateAllowance()
    {
        return 15;
    }
}
