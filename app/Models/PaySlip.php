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

        $query->whereHas('employee', function (Builder $query) use ($request) {

            if ($request->has('locale_id')) {
                $query->whereIn('locale_id', $request->locale_id);
            }

        });

        if ($request->has(['from', 'to'])) {

            $query->whereDate('from', '=', $request->from)
                  ->whereDate('to', '=', $request->to);

        }
        
        return $query;
    }

    public function scopeCheckPeriod($query, $request)
    {
        $from = null;
        $to   = null;

        if ($request->has(['from', 'to'])) {
            $from = $request->from;
            $to   = $request->to;
        } elseif ($request->has('attended_at')) {
            $from = $attended_at = $request->attended_at;
            $to   = $attended_at;
        }

        return $query->whereDate('from', '<=', $to)
            ->whereDate('to', '>=', $from);
    }

    public function ca_children()
    {
        return $this->hasOne(CA_CHILDREN::class, 'payslip_id');
    }

    public function government_loan_payment()
    {
        return $this->hasOne(GovernmentLoanPayment::class, 'payslip_id');
    }

    public function government_loan_payments()
    {
        return $this->hasMany(GovernmentLoanPayment::class, 'payslip_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
