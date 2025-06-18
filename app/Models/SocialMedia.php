<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scope untuk social media aktif
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

    // Accessor untuk platform icon
    public function getPlatformIconAttribute()
    {
        $icons = [
            'facebook' => 'bi bi-facebook',
            'instagram' => 'bi bi-instagram', 
            'twitter' => 'bi bi-twitter-x',
            'linkedin' => 'bi bi-linkedin',
            'youtube' => 'bi bi-youtube',
            'tiktok' => 'bi bi-tiktok',
            'whatsapp' => 'bi bi-whatsapp',
            'telegram' => 'bi bi-telegram',
            'website' => 'bi bi-globe2',
            'email' => 'bi bi-envelope',
        ];

        $platform = strtolower($this->platform);
        return $icons[$platform] ?? 'bi bi-link';
    }

    // Accessor untuk platform color
    public function getPlatformColorAttribute()
    {
        $colors = [
            'facebook' => 'blue',
            'instagram' => 'pink', 
            'twitter' => 'cyan',
            'linkedin' => 'blue',
            'youtube' => 'red',
            'tiktok' => 'dark',
            'whatsapp' => 'green',
            'telegram' => 'blue',
            'website' => 'gray',
            'email' => 'orange',
        ];

        $platform = strtolower($this->platform);
        return $colors[$platform] ?? 'gray';
    }

    // Get available platforms
    public static function getAvailablePlatforms()
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'twitter' => 'Twitter',
            'linkedin' => 'LinkedIn',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'telegram' => 'Telegram',
            'website' => 'Website',
            'email' => 'Email',
        ];
    }
}