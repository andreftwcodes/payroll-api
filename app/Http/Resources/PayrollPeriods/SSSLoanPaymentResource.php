<?php

namespace App\Http\Resources\PayrollPeriods;

use Illuminate\Http\Resources\Json\JsonResource;

class SSSLoanPaymentResource extends JsonResource
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
            'sss_loan_id' => $this->sss_loan_id
        ];
    }
}
