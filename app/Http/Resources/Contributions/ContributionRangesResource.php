<?php

namespace App\Http\Resources\Contributions;

use Illuminate\Http\Resources\Json\JsonResource;

class ContributionRangesResource extends JsonResource
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
            'from_display' => $this->formattedAmount($this->from),
            'to_display' => $this->formattedAmount($this->to),
            'er_display' => $this->formattedAmount($this->er),
            'ee_display' => $this->formattedAmount($this->ee),
            'status' => true
        ];
    }

    protected function formattedAmount($amount)
    {
        return number_format($amount, 2);
    }
}
