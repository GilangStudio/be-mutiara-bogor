<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessibilityPageBanner extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function accessibilityPage()
    {
        return $this->belongsTo(AccessibilityPage::class);
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