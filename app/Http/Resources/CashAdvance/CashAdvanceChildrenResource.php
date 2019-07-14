<?php

namespace App\Http\Resources\CashAdvance;

use Illuminate\Http\Resources\Json\JsonResource;

class CashAdvanceChildrenResource extends JsonResource
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
            'date' => $this->date,
            'credit' => $this->formatted($this->credit),
            'debit' => $this->formatted($this->debit)
        ];
    }

    protected function formatted($value)
    {
        return !is_null($value) ? number_format($value, 2) : null;
    }
}
