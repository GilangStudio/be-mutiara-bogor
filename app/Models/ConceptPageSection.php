<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptPageSection extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeImageLeft($query)
    {
        return $query->where('layout_type', 'image_left');
    }

    public function scopeImageRight($query)
    {
        return $query->where('layout_type', 'image_right');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getLayoutTextAttribute()
    {
        return $this->layout_type === 'image_left' ? 'Image Left' : 'Image Right';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}