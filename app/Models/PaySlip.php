<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PaySlip extends Model
{
    protected $table = 'payslips';

    protected $fillable = [
        'to', 'from', 'contributions'
    ];

    public function scopeApplyFilters($query, $request)
    {
        $query->whereHas('payslip_periods', function (Builder $query) use ($request) {

            if ($request->has(['from', 'to'])) {

                $date = [
                    $request->from,
                    $request->to
                ];

            } else {

                $date = [
                    today()->startOfMonth(),
                    today()
                ];

            }

            $query->whereBetween('date', $date);

        });

        $query->whereHas('employee', function (Builder $query) use ($request) {

            if ($request->has('locale_id')) {
                $query->whereIn('locale_id', $request->locale_id);
            }

        });

        return $query;
    }

    public function payslip_periods()
    {
        return $this->hasMany(PayslipPeriod::class, 'payslip_id');
    }

    public function ca_children()
    {
        return $this->hasOne(CA_CHILDREN::class, 'payslip_id');
    }

    public function sss_loan_payment()
    {
        return $this->hasOne(SSSLoanPayment::class, 'payslip_id');
    }

    public function attendance_statuses()
    {
        return $this->hasMany(AttendanceStatus::class, 'payslip_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
