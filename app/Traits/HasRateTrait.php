<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait HasRateTrait
{
    public function attachRate(Request $request)
    {
        $rate = ['amount' => $request->rate];

        if ($this->hasRate()) {
            $this->rate()->update($rate);
        } else {
            $this->rate()->create($rate);
        }

        $this->attachRateHistory($rate);
    }

    protected function attachRateHistory(array $rate)
    {
        if ($this->hasNoRateHistory()) {

            $this->rate_history()->create($rate);

        } elseif ($this->hasRateHistory()) {

            if ($this->canPersistNewRecord() && $this->rateHasChanged($rate)) {

                $this->rate_history()->create($rate);

            } else {

                $this->rate_history()->latest()->first()->update($rate);
                
            }
        }
    }

    protected function hasRate()
    {
        return (bool) $this->rate()->count();
    }

    protected function hasRateHistory()
    {
        return (bool) $this->rate_history()->count();
    }

    protected function hasNoRateHistory()
    {
        return !$this->hasRateHistory();
    }

    protected function canPersistNewRecord()
    {
        $created_at = $this->rate_history()->latest()->first()->created_at;
        return Carbon::parse($created_at)->startOfDay()->toDateTimeString() !== Carbon::today()->toDateTimeString();
    }

    protected function rateHasChanged(array $rate)
    {
        return floatval($this->rate_history()->latest()->first()->amount) !== floatval($rate['amount']);
    }

}
