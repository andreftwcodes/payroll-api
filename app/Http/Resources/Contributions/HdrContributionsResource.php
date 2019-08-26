<?php

namespace App\Http\Resources\Contributions;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class HdrContributionsResource extends JsonResource
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
            'flag' => $this->flag,
            'title' => $this->title,
            'used_at' => $this->used_at,
            'used_at_dsp' => $this->usedAtDisplay()
        ];
    }

    private function usedAtDisplay()
    {
        return Carbon::parse($this->used_at)->format('F Y');
    }
    
}
