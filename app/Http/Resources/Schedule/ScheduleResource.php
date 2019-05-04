<?php

namespace App\Http\Resources\Schedule;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'start' => $this->getFormattedTime($this->start),
            'end' => $this->getFormattedTime($this->end),
        ];
    }

    protected function getFormattedTime($time)
    {
        return Carbon::parse($time)->format('h:i');
    }
}
