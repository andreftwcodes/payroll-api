<?php

namespace App\Traits;

use Carbon\Carbon;

trait EmployeeTrait
{
    protected function getAge()
    {
        if (is_null($this->birthdate)) {
            return 'na';
        }

        return Carbon::parse($this->birthdate)->age;
    }

    protected function getBirthDate()
    {
        if (is_null($this->birthdate)) {
            return 'na';
        }

        return $this->birthdate;
    }

    protected function getRate()
    {
        if ($rate = $this->whenLoaded('rate')) {
            return $rate->amount;
        }
    }
}
