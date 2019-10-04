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
        
        $str   = '';

        foreach ($items = $this->time_logs()->get()->toArray() as $key => $item) {
            $str .= $this->getFormattedTime($item['time_in']) . ' - ' . $this->getFormattedTime($item['time_out']);
            $str .= !empty($str) && next($items) ? ' / ' : '';
        }

        $get_width = function ($items) {

            $width = 132;
            
            if ($count = count($items)) {
                $width = $count === 1 ? ($width - 8) : $width;
                $width = $count * $width;
            }

            return $width . 'px';

        };

        return [
            'width' => $get_width($items),
            'items' => $str
        ];
    }

}
