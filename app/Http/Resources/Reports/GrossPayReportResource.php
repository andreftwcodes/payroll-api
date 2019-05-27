<?php

namespace App\Http\Resources\Reports;

use Carbon\Carbon;
use App\Libraries\Calculator;
use App\Traits\AttendanceTrait;
use App\Libraries\TimeCalculator;
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
            'rate' => $this->amount,
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

        return (new Calculator($this->getMappedCalcData()))->getFormattedGrossPay();
    }

    protected function getTimeCalcInstance()
    {
        return (new TimeCalculator([
            'sched_start_1'     => $this->sched_start_1,
            'sched_end_1'       => $this->sched_end_1,
            'sched_start_2'     => $this->sched_start_2,
            'sched_end_2'       => $this->sched_end_2,
            'timeIn'    => $this->start,
            'timeOut'   => $this->end,
            // 'special_person' => $this->special_person,
            'overtime'       => $this->overtime,
        ]));
    }

    protected function getMappedCalcData()
    {
        $tc = $this->getTimeCalcInstance();
        return [
            'rate' => $this->amount,
            'hours_worked' => $tc->getHours(),
            'over_time'    => $tc->getOverTime(),
            'shift'        => $tc->getShift()
        ];
    }

    protected function getDateDisplay()
    {
        return Carbon::parse($this->created_at)
            ->format('l m/d');
    }

}
