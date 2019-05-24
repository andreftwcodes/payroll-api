<?php

namespace App\Http\Resources\Schedule;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\AttendanceTrait;

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
            'name' => $this->getName()
        ];
    }

    protected function getName()
    {
        return $this->getFormattedTime($this->start_1) . ' - ' . $this->getFormattedTime($this->end_2);
    }
    
}
