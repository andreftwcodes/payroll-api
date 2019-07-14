<?php

namespace App\Http\Resources\CashAdvance;

use Illuminate\Http\Resources\Json\JsonResource;

class CashAdvanceResource extends JsonResource
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
            'amount_deductible' => $this->amount_deductible,
            'balance' => $this->balance()
        ];
    }

    protected function balance()
    {
        return number_format(
            $this->ca_children->sum('credit') - $this->ca_children->sum('debit'),
            2
        );
    }
}
