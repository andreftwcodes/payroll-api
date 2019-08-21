<?php

namespace App\Http\Resources\SSSLoan;

use App\Traits\SSSLoanTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SSSLoan\SSSLoanEmployeeResource;

class SSSLoanIndexResource extends JsonResource
{
    use SSSLoanTrait;
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
            'loan_no' => $this->loan_no,
            'employee' => new SSSLoanEmployeeResource($this->whenLoaded('employee')),
            'amount_loaned' => $this->amount_loaned,
            'amount_loaned_dsp' => $this->formattedAmount($this->amount_loaned),
            'amortization_amount' => $this->amortization_amount,
            'payment_terms' => $this->payment_terms,
            'date_loaned' => $this->date_loaned,
            'balance' => $this->formattedAmount($this->balance()),
            'progress' => "{$this->progress()}%"
        ];
    }

}
