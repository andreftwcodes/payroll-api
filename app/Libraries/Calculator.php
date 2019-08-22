<?php

namespace App\Libraries;

class Calculator
{
    const OVERTIME_RATE = 130; //percent

    const NIGHT_DIFFERENTIAL = 10; //percent

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getGrossPay()
    {
        return $this->computeGrossPay() + $this->nightShiftPay() + $this->overTimePay();
    }

    public function getFormattedGrossPay()
    {
        return '₱ ' . number_format($this->getGrossPay(), 2);
    }

    public function getOverTimeHours()
    {
        return $this->overTimeHours();
    }

    public function getUnderTimeHours()
    {
        return $this->underTimeHours();
    }

    protected function computeGrossPay()
    {
        return $this->minutesWorked() * $this->ratePerMinute();
    }

    protected function workingHours()
    {
        return $this->data['working_hours'];
    }

    protected function minutesWorked()
    {
        return $this->toMinutes(
            $this->isOverTime() ? $this->workingHours() : $this->data['hours_worked']
        );
    }

    protected function ratePerMinute()
    {
        return $this->data['rate'] / $this->toMinutes($this->workingHours());
    }

    protected function toMinutes($value)
    {
        return $value * 60;
    }

    protected function hasOverTimePremium()
    {
        return (bool) $this->data['overtime'];
    }

    protected function isNightShift()
    {
        return $this->data['shift'] === 'night';
    }

    protected function isOverTime()
    {
        return $this->data['hours_worked'] > $this->workingHours();
    }

    protected function isUnderTime()
    {
        return $this->data['hours_worked'] < $this->workingHours();
    }

    public function overTimePay()
    {
        $minutesWorked = $this->toMinutes(
            $this->overTimeHours()
        );

        $amount = $minutesWorked * $this->ratePerMinute();

        if ($this->hasOverTimePremium()) {
            $amount *= (self::OVERTIME_RATE / 100);
        }

        return $amount;
    }

    public function underTimePay()
    {
        $minutesUnWorked = $this->toMinutes(
            $this->underTimeHours()
        );

        $amount = $minutesUnWorked * $this->ratePerMinute();

        return $amount;
    }

    protected function overTimeHours()
    {
        $hours = 0;

        if ($this->isOverTime()) {
            $hours = $this->data['hours_worked'] - $this->workingHours();
        }

        return $hours;
    }

    protected function underTimeHours()
    {
        $hours = 0;

        if ($this->isUnderTime()) {
            if (($hours_worked = $this->data['hours_worked']) > 0) {
                $hours = $this->workingHours() - $hours_worked;
            }
        }

        return $hours;
    }

    protected function nightShiftPay() //@brb
    {
        $amount = 0;

        if ($this->isNightShift()) {
            $amount = $this->ratePerMinute() * (self::NIGHT_DIFFERENTIAL / 100);
            $amount *= $this->minutesWorked();  
        }

        return $amount;
    }

}
