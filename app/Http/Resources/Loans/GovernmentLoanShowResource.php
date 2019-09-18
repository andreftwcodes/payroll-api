<?php

namespace App\Http\Resources\Loans;

use App\Traits\LoanTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Loans\GovernmentLoanEmployeeResource;
use App\Http\Resources\Loans\GovernmentLoanPaymentsResource;

class GovernmentLoanShowResource extends JsonResource
{
    use LoanTrait;
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
            'ref_no' => $this->ref_no,
            'subject' => strtoupper($this->subject),
            'employee' => new GovernmentLoanEmployeeResource($this->whenLoaded('employee')),
            'amount_loaned' => $this->formattedAmount($this->amount_loaned),
            'loaned_at' => $this->loaned_at,
            'balance' => $this->formattedAmount($this->balance()),
            'progress' => "{$this->progress()}%",
            'payments' => GovernmentLoanPaymentsResource::collection(
                $this->payments()
            )
        ];
    }
}
