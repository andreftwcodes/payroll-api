<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sss_contributions extends Model
{
    protected $fillable = [
        'key', 'title', 'status'
    ];

    public function table_ranges()
    {
        return $this->hasMany(contribution_ranges::class, 'parent_key', 'key');
    }
}
