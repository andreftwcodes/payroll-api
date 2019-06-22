<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class contribution_range extends Model
{
    protected $fillable = [
        'from', 'to', 'er', 'ee'
    ];

    public function scopeApplyFilter(Builder $builder, $grossPay)
    {
        return $builder->where([
            ['from', '<=', $grossPay],
            ['to', '>=', $grossPay],
        ]);
    }
}
