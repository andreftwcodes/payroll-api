<?php

namespace App\Http\Resources\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceTimeLogsResource extends JsonResource
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
            'time_in' => Carbon::parse($this->time_in)->toTimeString(),
            'time_out' => Carbon::parse($this->time_out)->toTimeString()
        ];
    }
}
