<?php

namespace App\Http\Resources\Loans;

use Carbon\Carbon;
use App\Traits\LoanTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Loans\GovernmentLoanEmployeeResource;

class GovernmentLoanIndexResource extends JsonResource
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
            'amount_loaned' => $this->amount_loaned,
            'amount_loaned_dsp' => $this->formattedAmount($this->amount_loaned),
            'amortization_amount' => $this->amortization,
            'amortization_amount_dsp' => number_format($this->amortization, 2),
            'loaned_at' => $this->loaned_at,
            'loaned_dsp' => $this->formattedLoanedAt(),
            'balance' => $this->formattedAmount($this->balance()),
            'progress' => "{$this->progress()}%"
        ];
    }

    private function formattedLoanedAt()
    {
        return Carbon::parse($this->loaned_at)->format('F d, Y');
    }

}
