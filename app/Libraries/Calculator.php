<?php

namespace App\Libraries;

class Calculator
{
    const WORKING_HOURS = 8;

    const OVERTIME_RATE = 130;

    const NIGHT_DIFFERENTIAL = 10;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getGrossPay()
    {
        return $this->computeGrossPay() + $this->nightShift() + $this->overTime();
    }

    public function getFormattedGrossPay()
    {
        return '₱ ' . number_format($this->getGrossPay(), 2);
    }

    protected function computeGrossPay()
    {
        return $this->minutesWorked() * $this->ratePerMinute();
    }

    protected function minutesWorked()
    {
        return $this->toMinutes($this->data['hours_worked']);
    }

    protected function ratePerMinute()
    {
        return $this->data['rate'] / $this->toMinutes(self::WORKING_HOURS);
    }

    protected function toMinutes($value)
    {
        return $value * 60;
    }

    protected function isNightShift()
    {
        return $this->data['shift'] === 'night';
    }

    protected function overTime()
    {
        $minutesWorked = $this->toMinutes(
            $this->data['over_time']
        );

        $amount = $minutesWorked * $this->ratePerMinute();

        if ($this->isNightShift()) {
            $amount += $this->nightShift($minutesWorked);
        }

        $amount *= (self::OVERTIME_RATE / 100);

        return $amount;
    }

    protected function nightShift($minutesWorked = null)
    {
        $amount = 0;

        if ($this->isNightShift()) {
            $amount = $this->ratePerMinute() * (self::NIGHT_DIFFERENTIAL / 100);
            $amount *= !is_null($minutesWorked) ? $minutesWorked : $this->minutesWorked(); 
        }

        return $amount;
    }

}
