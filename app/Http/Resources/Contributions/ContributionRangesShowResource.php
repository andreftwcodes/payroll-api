<?php

namespace App\Http\Resources\Contributions;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Contributions\ContributionRangesResource;

class ContributionRangesShowResource extends JsonResource
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
            'title' => $this->title,
            'table' => ContributionRangesResource::collection(
                $this->whenLoaded('ranges')
            )
        ];
    }
    
}
