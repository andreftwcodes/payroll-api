<?php

namespace App\Models;

use App\Traits\HasRateTrait;
use App\Traits\HasOtherTrait;
use App\Traits\HasScheduleTrait;
use App\Traits\HasDeductionTrait;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasRateTrait, HasScheduleTrait, HasDeductionTrait, HasOtherTrait;

    protected $fillable = [
        'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'status'
    ];

    public function addEmployee($request)
    {
        return $this->create(
            $request->only(
                'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id'
            )
        );
    }

    public function updateEmployee($request)
    {
        $this->update(
            $request->only(
                'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'status'
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

    public function deductions()
    {
        return $this->belongsToMany(Deduction::class, 'employee_deductions');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
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

}
