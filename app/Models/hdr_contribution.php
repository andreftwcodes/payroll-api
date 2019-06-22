<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class hdr_contribution extends Model
{
    protected $fillable = [
        'flag', 'title', 'status'
    ];

    public function scopeSss(Builder $builder)
    {
        return $builder->where('flag', 'sss')->active();
    }

    public function scopePagibig(Builder $builder)
    {
        return $builder->where('flag', 'pagibig')->active();
    }

    public function scopePhilhealth(Builder $builder)
    {
        return $builder->where('flag', 'philhealth')->active();
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('status', 1);
    }

    public function ranges()
    {
        return $this->hasMany(contribution_range::class);
    }
}
