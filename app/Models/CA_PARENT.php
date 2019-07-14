<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CA_PARENT extends Model
{
    protected $table = 'ca_parents';

    protected $fillable = [
        'amount_deductible'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function ca_children()
    {
        return $this->hasMany(CA_CHILDREN::class, 'ca_parents_id', 'id');
    }
}
