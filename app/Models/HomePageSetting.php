<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Accessors for image URLs
    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image_path ? asset('storage/' . $this->banner_image_path) : null;
    }

    public function getBannerVideoUrlAttribute()
    {
        return $this->banner_video_path ? asset('storage/' . $this->banner_video_path) : null;
    }

    public function getAboutImageUrlAttribute()
    {
        return $this->about_image_path ? asset('storage/' . $this->about_image_path) : null;
    }

    public function getFeaturesImageUrlAttribute()
    {
        return $this->features_image_path ? asset('storage/' . $this->features_image_path) : null;
    }

    public function getLocationImageUrlAttribute()
    {
        return $this->location_image_path ? asset('storage/' . $this->location_image_path) : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper method to get banner media URL
    public function getBannerMediaUrlAttribute()
    {
        return $this->banner_type === 'video' ? $this->banner_video_url : $this->banner_image_url;
    }

    // Helper method to check if banner is video
    public function getIsBannerVideoAttribute()
    {
        return $this->banner_type === 'video';
    }
}