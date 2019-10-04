<?php

namespace App\Http\Resources\Reports;

use App\Traits\AttendanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeSheetChildrenResource extends JsonResource
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
            'date'      => $this->attended_at,
            'locale'    => $this->whenLoaded('locale'),
            'time_logs' => $this->__timeLogs(),
            'hours'     => $this->timeCalc()->getHours(),
            'hours_dsp' => $this->timeCalc()->getFormattedHours(),
            'remarks'   => $this->getRemark()
        ];
    }

    private function __timeLogs()
    {
        return $this->time_logs()->get()->map(function ($item, $key) {
            return array(
                'time_in'  => $this->getFormattedTime($item['time_in']),
                'time_out' => $this->getFormattedTime($item['time_out'])
            );
        });
    }

}
