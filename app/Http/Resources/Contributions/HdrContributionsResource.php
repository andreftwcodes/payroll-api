<?php

namespace App\Http\Resources\Contributions;

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
            'status' => $this->status,
            'status_display' => $this->getStatus(),
        ];
    }

    protected function getStatus()
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
