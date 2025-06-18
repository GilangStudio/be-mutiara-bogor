<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitGallery extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}