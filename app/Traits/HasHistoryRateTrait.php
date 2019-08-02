<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\HistoryRate;
use Illuminate\Http\Request;

trait HasHistoryRateTrait
{
    public function attachRateHistory($request)
    {

        $rate = $request->only('rate');

        if ($this->hasRateHistory()) {
            
            if ($this->canPersistNewRecord() && $this->rateHasChanged($request->rate)) {

                $this->rate_history()->create($rate);

            } else {

                $this->rate_history()->latest()->first()->update($rate);
                
            }

        } else {

            $this->rate_history()->create($rate);

        }
    }

    public function HistoryRateMappedData()
    {
        $history_rate = $this->rate_history()->get();

        return $history_rate->map(function ($item, $key) use ($history_rate) {
            $item->to = $this->getToDate($history_rate, $key);
            return $item;
        });
    }

    protected function hasRateHistory()
    {
        return $this->rate_history()->exists();
    }

    protected function canPersistNewRecord()
    {
        $created_at = $this->rate_history()->latest()->first()->created_at;
        return Carbon::parse($created_at)->startOfDay()->toDateTimeString() !== Carbon::today()->toDateTimeString();
    }

    protected function rateHasChanged($rate)
    {
        return floatval($this->rate_history()->latest()->first()->rate) !== floatval($rate);
    }

    protected function getToDate($history_rate, $key)
    {
        $selectedNextDate = @$history_rate->toArray()[$summedKey = $key + 1]['created_at'];

        if ($history_rate->count() === $summedKey) {
            return null;
        }

        return Carbon::parse($selectedNextDate)->subDay();
    }
}
