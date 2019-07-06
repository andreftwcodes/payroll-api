<?php

namespace App\Http\Resources\Schedules;

use Illuminate\Http\Resources\Json\JsonResource;

class SchedulesResource extends JsonResource
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
            'day' => $this->day,
            'day_dsp' => $this->dayInWords(),
            'start_1' => $this->start_1,
            'end_1' => $this->end_1,
            'start_2' => $this->start_2,
            'end_2' => $this->end_2,
            'status' => $this->status
        ];
    }
    
    protected function dayInWords()
    {
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];

        return $days[$this->day];
    }
}
