<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class hdr_contribution extends Model
{
    protected $fillable = [
        'flag', 'title', 'status'
    ];

    public function ranges()
    {
        return $this->hasMany(contribution_range::class);
    }
}
