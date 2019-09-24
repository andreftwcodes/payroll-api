<?php

namespace App\Http\Resources\Reports;

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
            'disabled'          => $this->disabled(),
            'amount_deductible' => $this->amount_deductible,
            'balance'           => $this->balance(),
            'display'           => " | Balance: " . $this->formattedBalance(),
        ];
    }

    protected function disabled()
    {
        return !$this->balance();
    }

    protected function balance()
    {
        return $this->ca_children->sum('credit') - $this->ca_children->sum('debit');
        
    }

    protected function formattedBalance()
    {
        return number_format(
            $this->balance(),
            2
        );
    }
}
