<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status === 'published' ? 'Published' : 'Draft';
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getExcerptAttribute()
    {
        // return strip_tags($this->content);

        return Str::limit(strip_tags($this->content), 100);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}