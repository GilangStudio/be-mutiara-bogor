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

    public function getRouteKeyName()
    {
        return 'slug';
    }
}