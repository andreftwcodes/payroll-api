<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasOtherTrait
{
    public function attachOther(Request $request)
    {
        if ($this->hasOther()) {

            $this->other()->update(
                $request->only('contributions', 'night_shift', 'overtime')
            );
            
            return false;
        }

        $this->other()->create(
            $request->only('contributions', 'night_shift', 'overtime')
        );
    }

    protected function hasOther()
    {
        return (bool) $this->other()->count();
    }
}
