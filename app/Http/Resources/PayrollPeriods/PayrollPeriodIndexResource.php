<?php

namespace App\Http\Resources\PayrollPeriods;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PayrollPeriods\EmployeeResource;
use App\Http\Resources\PayrollPeriods\CashAdvanceResource;
use App\Http\Resources\PayrollPeriods\SSSLoanPaymentResource;

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
            'from' => $this->fromDate(),
            'to' => $this->toDate(),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'contributions' => $this->contributions,
            'cash_advance' => new CashAdvanceResource($this->whenLoaded('ca_children')),
            'sss_loan_payment' => new SSSLoanPaymentResource($this->whenLoaded('sss_loan_payment')),
            'period_dsp' => $this->periodDsp(),
            'created_at' => $this->createdAt()
        ];
    }

    private function periods()
    {
        return $this->whenLoaded('payslip_periods');
    }

    private function periodDsp()
    {
        $fromDate = Carbon::parse($this->fromDate())->format('F d - ');
        $toDate   = Carbon::parse($this->toDate())->format('d, Y');
        return $fromDate.$toDate;
    }

    private function fromDate()
    {
        return $this->periods()->first()->date;
    }

    private function toDate()
    {
        return $this->periods()->last()->date;
    }

    private function createdAt()
    {
        return Carbon::parse($this->created_at)->toDayDateTimeString();
    }
}
