<?php

namespace App\Http\Resources\Schedule;

use App\Traits\AttendanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleIndexResource extends JsonResource
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
            'first_quarter' => $this->firstQuarter(),
            'second_quarter' => $this->secondQuarter(),
            'status' => $this->status
        ];
    }

    private function firstQuarter()
    {
        return $this->getFormattedTime($this->start_1) . ' - ' . $this->getFormattedTime($this->end_1);
    }

    private function secondQuarter()
    {
        return $this->getFormattedTime($this->start_2) . ' - ' . $this->getFormattedTime($this->end_2);
    }
}
