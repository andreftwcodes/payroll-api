<?php

namespace App\Models;

use App\Traits\HasRateTrait;
use App\Traits\HasOtherTrait;
use App\Traits\HasScheduleTrait;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasRateTrait, HasScheduleTrait, HasOtherTrait;

    protected $fillable = [
        'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'payment_period', 'status'
    ];

    public function addEmployee($request)
    {
        return $this->create(
            $request->only(
                'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'payment_period'
            )
        );
    }

    public function updateEmployee($request)
    {
        $this->update(
            $request->only(
                'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'payment_period', 'status'
            )
        );

        return $this;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function rate()
    {
        return $this->hasOne(Rate::class);
    }

    public function rate_history()
    {
        return $this->hasMany(HistoryRate::class);
    }

    public function schedules()
    {
        return $this->hasMany(employee_schedules::class);
    }

    public function locale()
    {
        return $this->hasOne(Locale::class, 'id', 'locale_id');
    }

    public function other()
    {
        return $this->hasOne(Other::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function ca_parent()
    {
        return $this->hasOne(CA_PARENT::class);
    }

}
