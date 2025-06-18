<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitSpecification extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}