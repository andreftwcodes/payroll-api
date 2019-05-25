<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $fillable = [
        'name'
    ];

    public function createDeduction($request)
    {
        return $this->create(
            $request->only('name')
        );
    }

    public function updateDeduction($request)
    {
        $this->update(
            $request->only('name')
        );

        return $this;

    }
}
