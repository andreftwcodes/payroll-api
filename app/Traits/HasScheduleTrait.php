<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasScheduleTrait
{
    public function attachSchedule(Request $request)
    {
        $data = $request->only('start', 'end');

        if ($this->hasSchedule()) {
            $this->schedule()->update($data);
            return false;
        }

        $this->schedule()->create($data);
    }

    protected function hasSchedule()
    {
        return (bool) $this->schedule()->count();
    }
}
