<?php

namespace App\Http\Resources\Loans;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernmentLoanPaymentsResource extends JsonResource
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
            'amortization' => $this->government_loan->amortization,
            'paid_at' => $this->paidAt()
        ];
    }

    private function paidAt()
    {
        return Carbon::parse($this->paid_at)->format('F d, Y');
    }

}
