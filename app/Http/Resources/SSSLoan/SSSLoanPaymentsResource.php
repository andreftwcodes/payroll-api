<?php

namespace App\Http\Resources\SSSLoan;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SSSLoanPaymentsResource extends JsonResource
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
            'paid_at' => $this->paidAt(),
            'transact_by' => $this->transactBy(),
            'is_payroll' => $this->isPayroll()
        ];
    }

    private function paidAt()
    {
        return Carbon::parse($this->paid_at)->format('F d, Y');
    }

    private function transactBy()
    {
        return $this->isPayroll() ? 'Payroll' : 'Manual';
    }

    private function isPayroll()
    {
        return !is_null($this->payslip_id);
    }
}
