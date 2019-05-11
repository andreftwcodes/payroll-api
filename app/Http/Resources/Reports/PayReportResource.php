<?php

namespace App\Http\Resources\Reports;

use Carbon\Carbon;
use App\Libraries\Calculator;
use Illuminate\Http\Resources\Json\JsonResource;

class PayReportResource extends JsonResource
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
            'dailyRate' => $this->employee->rate->amount,
            'pay' => $this->getPay(),
            'date' => Carbon::parse($this->created_at)->toDateString()
        ];
    }

    protected function getPay()
    {
        return (new Calculator($this->getMappedData()))->getFormattedGrossPay();
    }

    protected function getMappedData()
    {
        return (new \Illuminate\Http\Request())->replace([
            'dailyRate' => $this->employee->rate->amount,
            'start' => $this->employee->schedule->start,
            'end' => $this->employee->schedule->end,
            'timeIn' => $this->start,
            'timeOut' => $this->end
        ]);
    }
}
