<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimeScheduleTrait
{
    protected function mappedData($data)
    {   
        /**
         * For "Night Shift"
         * Switch and convert meridiem Time direction from AM to PM
         */
        if ($this->getScheduleShiftType($data) === 'night') {

            $type_start = 'am';
            $type_end   = 'pm';

            $data['sched_start_1'] = $this->flipTime($data['sched_start_1'], $type_start);
            $data['sched_end_1']   = $this->flipTime($data['sched_end_1'], $type_end);
            $data['sched_start_2'] = $this->flipTime($data['sched_start_2'], $type_end);
            $data['sched_end_2']   = $this->flipTime($data['sched_end_2'], $type_end);

            if ($this->isAm($data['timeIn']) && $this->isAm($data['timeOut'])) {
                $meridiem_in  = 'pm';
                $meridiem_out = 'pm';
            } else if ($this->isPm($data['timeIn']) && $this->isPm($data['timeOut'])) {
                $meridiem_in  = 'am';
                $meridiem_out = 'am';
            } elseif ($this->isPm($data['timeIn']) && $this->isAm($data['timeOut'])) {
                $meridiem_in  = 'am';
                $meridiem_out = 'pm';
            }

            $data['timeIn']  = $this->flipTime($data['timeIn'], $meridiem_in);
            $data['timeOut'] = $this->flipTime($data['timeOut'], $meridiem_out);

            $data['shift'] = 'night';
            
        } else {
            $data['shift'] = 'morning';
        }

        return $data;
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

    protected function getScheduleShiftType($data)
    {
        return $this->getShiftName($data['sched_start_1'], $data['sched_end_2']);
    }

    protected function getTimeInAndOutShiftType($data)
    {
        return $this->getShiftName($data['timeIn'], $data['timeOut']);
    }

    protected function getShiftName($start = null, $end = null)
    {
        if (is_null($start) || is_null($end)) {
            return null;
        }

        $start = Carbon::parse($start)->format('A');
        $end   = Carbon::parse($end)->format('A');

        if ($start === 'AM' && $end === 'PM' || $start === 'AM' && $end === 'AM') {
            $shift_name = 'morning';
        } elseif ($start === 'PM' && $end === 'AM' || $start === 'PM' && $end === 'PM') {
            $shift_name = 'night';
        }

        return $shift_name;
    }

    protected function isAm($time)
    {
        return Carbon::parse($time)->format('A') === 'AM';
    }

    protected function isPm($time)
    {
        return !$this->isAm($time);
    }

}
