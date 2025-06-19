<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptPage extends Model
{
    protected $guarded = ['id'];

    // Accessors
    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image_path ? asset('storage/' . $this->banner_image_path) : null;
    }

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

    public function getRouteKeyName()
    {
        return 'slug';
    }
}