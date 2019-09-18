<?php

namespace App\Models;

use App\Traits\HasOtherTrait;
use App\Traits\HasHistoryRateTrait;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasHistoryRateTrait, HasOtherTrait;

    protected $fillable = [
        'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'rate', 'payment_period', 'hired_at', 'status'
    ];

    public function addEmployee($request)
    {
        return $this->create(
            $request->only(
                'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'rate', 'payment_period', 'hired_at', 'status'
            )
        );
    }

    public function updateEmployee($request)
    {
        $this->update(
            $request->only(
                'firstname', 'middlename', 'lastname', 'gender', 'contact', 'birthdate', 'locale_id', 'rate', 'payment_period', 'hired_at', 'status'
            )
        );

        return $this;
    }

    public function scopeApplyFilter($query, $filter)
    {
        if ($filter->filled('payment_period')) {
            if (($payment_period = $filter->payment_period) !== 'all') {
                $query->where('payment_period', $payment_period);
            }
        }

        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
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

    public function government_loan()
    {
        return $this->hasOne(GovernmentLoan::class);
    }

    public function government_loans()
    {
        return $this->hasMany(GovernmentLoan::class);
    }

    public function payslips()
    {
        return $this->hasMany(PaySlip::class);
    }

}
