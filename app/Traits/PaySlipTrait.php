<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait PaySlipTrait
{
    private function periodHasEndOfMonthDate()
    {
        
        $period = $this->period();

        $endOfMonthDate = Carbon::parse($from = $period['from'])->endOfMonth()->toDateString();
        
        return in_array(
            $endOfMonthDate,
            $this->dateRange($from, $period['to'])
        );

    }

    private function period()
    {
        return session('period');
    }

    private function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {
        
        $dates   = [];
        $current = strtotime($first);
        $last    = strtotime($last);

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
    
        return $dates;
    }

}
