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

    public function scopeFilterByYearMonth($query, $request)
    {
        $query->whereHas('payslip_periods', function (Builder $query) use ($request) {

            $date = now()->format('Y-m');

            if ($request->filled('year_month')) {
                $date = $request->year_month;
            }

            $date = explode('-', $date);
            
            $query->whereYear('date', $date[0])->whereMonth('date', $date[1]);

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
