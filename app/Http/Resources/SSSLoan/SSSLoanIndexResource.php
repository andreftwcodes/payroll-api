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
            'amount_loaned' => $this->formattedAmount($this->amount_loaned),
            'date_loaned' => $this->date_loaned,
            'balance' => $this->formattedAmount($this->balance()),
            'progress' => "{$this->progress()}%"
        ];
    }

}
