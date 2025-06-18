<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function galleries()
    {
        return $this->hasMany(UnitGallery::class)->ordered();
    }

    public function specifications()
    {
        return $this->hasMany(UnitSpecification::class);
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

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getMainImageUrlAttribute()
    {
        return $this->main_image_path ? asset('storage/' . $this->main_image_path) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner_path ? asset('storage/' . $this->banner_path) : null;
    }

    public function getFloorPlanImageUrlAttribute()
    {
        return $this->floor_plan_image_path ? asset('storage/' . $this->floor_plan_image_path) : null;
    }
}