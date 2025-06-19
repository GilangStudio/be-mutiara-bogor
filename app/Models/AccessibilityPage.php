<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessibilityPage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function bannerImages()
    {
        return $this->hasMany(AccessibilityPageBanner::class)->ordered();
    }

    // Accessors
    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: $this->title;
    }

    public function getMetaDescriptionDisplayAttribute()
    {
        return $this->meta_description ?: '';
    }

    public function getMetaKeywordsDisplayAttribute()
    {
        return $this->meta_keywords ?: '';
    }
}