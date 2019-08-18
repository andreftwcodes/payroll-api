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
            'payslip_id' => $this->payslip_id,
            'paid_at' => $this->paidAt(),
            'processed_by' => $this->processedBy()
        ];
    }

    private function paidAt()
    {
        return Carbon::parse($this->paid_at)->format('F d, Y');
    }

    private function processedBy()
    {
        return is_null($this->payslip_id) ? 'Manual' : 'Payroll';
    }
}