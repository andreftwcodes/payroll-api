<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contribution_range extends Model
{
    protected $fillable = [
        'from', 'to', 'er', 'ee'
    ];
}
