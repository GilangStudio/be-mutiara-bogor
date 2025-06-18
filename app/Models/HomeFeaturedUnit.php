<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFeaturedUnit extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Accessor for status text
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // Helper method to get unit with project info
    public function scopeWithUnitProject($query)
    {
        return $query->with(['unit.project']);
    }
}