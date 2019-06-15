<?php

namespace App\Http\Resources\Rate;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RateHistoryResource extends JsonResource
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
            'rate' => $this->formattedRate(),
            'from' => $this->getFrom(),
            'to' => $this->getTo()
        ];
    }

    protected function getFrom()
    {
        return Carbon::parse($this->created_at)->toDateString();
    }

    protected function getTo()
    {
        if (is_null($this->to)) {
            return 'Present';
        }

        return Carbon::parse($this->to)->toDateString();
    }

    protected function formattedRate()
    {
        return number_format($this->amount, 2);
    }
}
