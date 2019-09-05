<?php

namespace App\Http\Resources\PayrollPeriods;

use Carbon\Carbon;
use App\Libraries\PaySlip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PayrollPeriods\EmployeeResource;

class PayrollPeriodIndexResource extends JsonResource
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
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'period_dsp' => $this->periodDsp(),
            'created_at' => $this->createdAt(),
            'print_url' => $this->printUrl()
        ];
    }

    private function periods()
    {
        return count($this->payslip_periods) ? $this->payslip_periods : null;
    }

    private function periodDsp()
    {
        $fromDate = Carbon::parse($this->fromDate())->format('F d - ');
        $toDate   = Carbon::parse($this->toDate())->format('d, Y');
        return "{$fromDate}{$toDate}";
    }

    private function fromDate()
    {
        return !is_null($period = $this->periods()) ? $period->first()->date : null;
    }

    private function toDate()
    {
        return !is_null($period = $this->periods()) ? $period->last()->date : null;
    }

    private function createdAt()
    {
        return Carbon::parse($this->created_at)->toDayDateTimeString();
    }

    private function printUrl()
    {
        return (new PaySlip(
            new Request([
                'from' => $this->fromDate(),
                'to'   => $this->toDate(),
                'contributions' => $this->contributions,
                'ca_amount_deductible' => !is_null($this->ca_children) ? $this->ca_children->debit : 0,
                'sss_loan_id' => !is_null($this->sss_loan_payment) ? $this->sss_loan_payment->sss_loan_id : null
            ]),
            $this->employee
        ))->getPrintUrl();
    }
}
