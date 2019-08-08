<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 'locale_id', 'amount', 'sched_start_1', 'sched_end_1', 'sched_start_2', 'sched_end_2', 'night_shift', 'overtime', 'attended_at'
    ];

    public function scopeApplyDateFilter(Builder $builder, $request)
    {
        $created_at = Carbon::today()->toDateString();

        if ($request->filled('created_at')) {
            $created_at = $request->created_at;
        }

        return $builder->whereDate('created_at', $created_at);
    }

    public function scopeApplyDateFilterPeriod(Builder $builder, $request)
    {
        return $builder->whereBetween('created_at', [
            Carbon::parse($request->from)->startOfDay()->toDateTimeString(),
            Carbon::parse($request->to)->endOfDay()->toDateTimeString()
        ]);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function time_logs()
    {
        return $this->hasMany(TimeLogs::class);
    }
}
