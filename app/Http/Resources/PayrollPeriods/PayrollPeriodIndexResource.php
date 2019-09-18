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
            'id'         => $this->id,
            'employee'   => new EmployeeResource($this->whenLoaded('employee')),
            'period_dsp' => $this->periodDsp(),
            'created_at' => $this->createdAt(),
            'print_url'  => $this->printUrl()
        ];
    }

    private function pluckedLoanIds()
    {
        return $this->government_loan_payments->pluck('government_loan_id');
    }

    private function periodDsp()
    {
        $fromDate = Carbon::parse($this->from)->format('F d - ');
        $toDate   = Carbon::parse($this->to)->format('d, Y');
        return "{$fromDate}{$toDate}";
    }

    private function createdAt()
    {
        return Carbon::parse($this->created_at)->toDayDateTimeString();
    }

    private function printUrl()
    {
        return (new PaySlip(
            new Request([
                'from' => $this->from,
                'to'   => $this->to,
                'contributions' => $this->contributions,
                'ca_amount_deductible' => !is_null($this->ca_children) ? $this->ca_children->debit : 0,
                'loan_id' => $this->pluckedLoanIds()
            ]),
            $this->employee
        ))->getPrintUrl();
    }
}
