<?php

namespace App\Http\Resources\Schedule;

use Carbon\Carbon;
use App\Traits\AttendanceTrait;
use App\Traits\TimeScheduleTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexScheduleResource extends JsonResource
{
    use AttendanceTrait, TimeScheduleTrait;
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
            'first_quarter' => $this->firstQuarter(),
            'second_quarter' => $this->secondQuarter(),
            'name' => $this->getName(),
            'shift' => ucfirst($this->getShiftType()),
            'status' => ucfirst($this->status),
        ];
    }

    protected function firstQuarter()
    {
        return $this->getFormattedTime($this->start_1) . ' - ' . $this->getFormattedTime($this->end_1);
    }

    protected function secondQuarter()
    {
        return $this->getFormattedTime($this->start_2) . ' - ' . $this->getFormattedTime($this->end_2);
    }

    protected function getName()
    {
        return $this->getFormattedTime($this->start_1) . ' - ' . $this->getFormattedTime($this->end_2);
    }

    protected function getShiftType()
    {
        if (is_null($start = $this->start_1) || is_null($end = $this->end_2)) {
            return null;
        }

        return $this->getShiftName($start, $end);
    }
}
