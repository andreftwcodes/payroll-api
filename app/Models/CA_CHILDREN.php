<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CA_CHILDREN extends Model
{
    protected $table = 'ca_childrens';

    protected $fillable = [
        'payslip_id', 'date', 'credit', 'debit'
    ];

    public function scopeOrderByDateAsc($query)
    {
        return $query->orderBy('date', 'asc');
    }

    public function ca_parent()
    {
        return $this->belongsTo(CA_PARENT::class, 'ca_parents_id');
    }
}
