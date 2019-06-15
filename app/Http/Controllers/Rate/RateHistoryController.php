<?php

namespace App\Http\Controllers\Rate;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\HistoryRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Rate\RateHistoryResource;

class RateHistoryController extends Controller
{
    public function show(Employee $employee)
    {
        return RateHistoryResource::collection(
            $this->HistoryRateMappedData($employee)
        );
    }

    protected function HistoryRateMappedData($employee)
    {
        return $this->mapHistoryRate(
            HistoryRate::where('employee_id', $employee->id)->get()
        );
    }

    protected function mapHistoryRate($history_rate)
    {
        return $history_rate->map(function ($item, $key) use ($history_rate) {
            $item->to = $this->getToDate($history_rate, $key);
            return $item;
        });
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
