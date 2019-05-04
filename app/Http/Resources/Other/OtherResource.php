<?php

namespace App\Http\Resources\Other;

use Illuminate\Http\Resources\Json\JsonResource;

class OtherResource extends JsonResource
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
            'special_person' => $this->special_person,
            'night_shift' => $this->night_shift,
            'overtime' => $this->overtime,
        ];
    }
}
