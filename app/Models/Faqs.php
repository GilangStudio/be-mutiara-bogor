<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scope untuk FAQ aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Accessor untuk status text
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // Accessor untuk category badge color
    public function getCategoryBadgeColorAttribute()
    {
        $colors = [
            'general' => 'blue',
            'project' => 'green', 
            'payment' => 'yellow',
            'technical' => 'red',
            'other' => 'gray'
        ];

        return $colors[$this->category] ?? 'gray';
    }

    // Get unique categories
    public static function getCategories()
    {
        return self::select('category')
                   ->distinct()
                   ->orderBy('category')
                   ->pluck('category')
                   ->toArray();
    }
}