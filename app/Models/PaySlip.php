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
        $date = [
            'from' => today()->startOfMonth(),
            'to'   => today()
        ];

        if ($request->has(['from', 'to'])) {

            $date = [
                'from' => $request->from,
                'to'   => $request->to
            ];

        }

        $query->whereDate('from', '=', $date['from'])->whereDate('to', '=', $date['to']);

        $query->whereHas('employee', function (Builder $query) use ($request) {

            if ($request->has('locale_id')) {
                $query->whereIn('locale_id', $request->locale_id);
            }

        });

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

    public function sss_loan_payment()
    {
        return $this->hasOne(SSSLoanPayment::class, 'payslip_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
