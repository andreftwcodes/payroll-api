<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 'locale_id', 'start', 'end'
    ];

    public function scopeApplyDateFilter(Builder $builder, $request)
    {
        $created_at = Carbon::today()->toDateString();

        if ($request->filled('created_at')) {
            $created_at = $request->created_at;
        }

        return $builder->whereDate('created_at', $created_at);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }
}
