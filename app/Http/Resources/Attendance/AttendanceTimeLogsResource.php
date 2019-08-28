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
            'time_in' => $this->timeIn(),
            'time_out' => $this->timeOut()
        ];
    }

    private function timeIn()
    {
        if (is_null($timeIn = $this->time_in)) {
            return null;
        }

        return Carbon::parse($timeIn)->toDateTimeString();
    }

    private function timeOut()
    {
        if (is_null($timeout = $this->time_out)) {
            return null;
        }

        return Carbon::parse($timeout)->toDateTimeString();
    }
}
