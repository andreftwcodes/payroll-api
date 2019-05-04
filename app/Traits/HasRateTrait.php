<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasRateTrait
{
    public function attachRate(Request $request)
    {
        $data = ['amount' => $request->rate];

        if ($this->hasRate()) {
            $this->rate()->update($data);
            return false;
        }

        $this->rate()->create($data);
    }

    protected function hasRate()
    {
        return (bool) $this->rate()->count();
    }
}
