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
            'amortization' => $this->sss_loan->amortization_amount,
            'paid_at' => $this->paidAt()
        ];
    }

    private function paidAt()
    {
        return Carbon::parse($this->paid_at)->format('F d, Y');
    }

}
