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
            'from' => $this->from,
            'to' => $this->to,
            'er' => $this->er,
            'ee' => $this->ee,
        ];
    }
}
