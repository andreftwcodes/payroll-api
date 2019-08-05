<?php

namespace App\Http\Resources\Schedule;

use App\Traits\AttendanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'start_1' => $this->start_1,
            'end_1' => $this->end_1,
            'start_2' => $this->start_2,
            'end_2' => $this->end_2,
            'description' => $this->description()
        ];
    }

    protected function description()
    {
        return $this->getFormattedTime($this->start_1) . ' - ' . $this->getFormattedTime($this->end_2);
    }
}
