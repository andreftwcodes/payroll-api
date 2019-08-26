<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class hdr_contribution extends Model
{
    protected $fillable = [
        'flag', 'title', 'used_at'
    ];

    public function scopeSss(Builder $builder)
    {
        return $builder->where('flag', 'sss');
    }

    public function scopePagibig(Builder $builder)
    {
        return $builder->where('flag', 'pagibig');
    }

    public function scopePhilhealth(Builder $builder)
    {
        return $builder->where('flag', 'philhealth');
    }

    public function scopeUsedAt(Builder $builder, $used_at)
    {
        return $builder->where('used_at', '<=', $used_at)->orderBy('used_at', 'desc');
    }

    public function ranges()
    {
        return $this->hasMany(contribution_range::class);
    }
}
