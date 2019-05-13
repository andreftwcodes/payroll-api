<?php

namespace App\Http\Resources\Reports;

use Carbon\Carbon;
use App\Libraries\Calculator;
use App\Traits\AttendanceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class GrossPayReportResource extends JsonResource
{
    use AttendanceTrait;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'remark' => $this->getRemark(),
            'grossPay' => $this->getGrossPay(),
            'dateDisplay' => $this->getDateDisplay()
        ];
    }

    protected function getGrossPay()
    {

        if (is_null($this->start) && is_null($this->end)) {
            return 'NA';
        }
        
        if (is_null($this->end)) {
            return 'TBD';
        }

        return (new Calculator($this->getMappedData()))->getFormattedGrossPay();
    }

    protected function getMappedData()
    {
        return (new \Illuminate\Http\Request())->replace([
            'rate'      => $this->amount,
            'start'     => $this->sched_start,
            'end'       => $this->sched_end,
            'special_person' => $this->special_person,
            'night_shift'    => $this->night_shift,
            'overtime'       => $this->overtime,
            'timeIn'    => $this->start,
            'timeOut'   => $this->end
        ]);
    }

    protected function getDateDisplay()
    {
        return Carbon::parse($this->created_at)
            ->format('l m/d');
    }

}
