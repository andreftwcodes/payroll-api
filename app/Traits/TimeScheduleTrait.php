<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimeScheduleTrait
{
    protected function mappedRequest($request)
    {   
        /**
         * For "Night Shift"
         * Switch and convert meridiem Time direction from AM to PM
         */
        if ($this->getScheduleShiftType($request) === 'night') {

            $type_start = 'am';
            $type_end   = 'pm';

            $request->sched_start_1 = $this->flipTime($request->sched_start_1, $type_start);
            $request->sched_end_1   = $this->flipTime($request->sched_end_1, $type_end);
            $request->sched_start_2 = $this->flipTime($request->sched_start_2, $type_end);
            $request->sched_end_2   = $this->flipTime($request->sched_end_2, $type_end);

            if ($this->getTimeInAndOutShiftType($request) === 'morning') {
                $type_start = 'pm';
            } else {
                $type_start = 'am';
            }

            $request->timeIn  = $this->flipTime($request->timeIn, $type_start);
            $request->timeOut = $this->flipTime($request->timeOut, 'pm');
            
        }

        return $request;
    }

    protected function flipTime($time = null, $type = null)
    {
        if (is_null($time) || is_null($type)) {
            return null;
        }

        $set = [];
        $key = 0;
        $value = '';
        $hour = substr($time, 0, 2);
        $type = strtolower($type);

        for ($i=12; $i <= 23; $i++) { 
            $set[$key] = (string) $i;
            $key++;
        }

        if (Carbon::parse($time)->format('a') === 'am' && $type === 'am') {
            return $time;
        }

        if (Carbon::parse($time)->format('a') === 'pm' && $type === 'pm') {
            return $time;
        }

        if ($type === 'am') {
            $value = array_search($hour, $set);
            $value = $value < 10 ? '0'.$value : $value;
        } else {
            $value = $set[(int) $hour];
        }

        return $value . substr($time, 2, 6);
    }

    protected function getScheduleShiftType($request)
    {
        if (is_null($start = $request->sched_start_1) || is_null($end = $request->sched_end_2)) {
            return null;
        }

        return $this->getShiftName($start, $end);
    }

    protected function getTimeInAndOutShiftType($request)
    {
        if (is_null($start = $request->timeIn) || is_null($end = $request->timeOut)) {
            return null;
        }

        return $this->getShiftName($start, $end);
    }

    protected function getShiftName($start, $end)
    {
        $start = Carbon::parse($start)->format('A');
        $end   = Carbon::parse($end)->format('A');

        if ($start === 'AM' && $end === 'PM' || $start === 'AM' && $end === 'AM') {
            $shift_name = 'morning';
        } elseif ($start === 'PM' && $end === 'AM' || $start === 'PM' && $end === 'PM') {
            $shift_name = 'night';
        }

        return $shift_name;
    }
}
