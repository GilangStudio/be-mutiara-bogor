<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->ordered();
    }

    public function facilityImages()
    {
        return $this->hasMany(FacilityImage::class)->ordered();
    }

    public function units()
    {
        return $this->hasMany(Unit::class)->ordered();
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

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getSiteplanImageUrlAttribute()
    {
        return $this->siteplan_image_path ? asset('storage/' . $this->siteplan_image_path) : null;
    }

    public function getBannerVideoUrlAttribute()
    {
        return $this->banner_video_path ? asset('storage/' . $this->banner_video_path) : null;
    }

    // Helper method untuk mendapatkan banner media URL
    public function getBannerMediaUrlAttribute()
    {
        return $this->banner_type === 'video' ? $this->banner_video_url : $this->banner_url;
    }

    // Helper method untuk cek apakah banner adalah video
    public function getIsBannerVideoAttribute()
    {
        return $this->banner_type === 'video';
    }

    // Meta SEO Accessors
    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: $this->name;
    }

    public function getMetaDescriptionDisplayAttribute()
    {
        return $this->meta_description ?: $this->short_description;
    }

    public function getMetaKeywordsDisplayAttribute()
    {
        return $this->meta_keywords ?: '';
    }
}
