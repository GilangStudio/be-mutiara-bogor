<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFeature extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Accessor for status text
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // Available icon options with Bootstrap Icons
    public static function getAvailableIcons()
    {
        return [
            'bi bi-house' => 'House',
            'bi bi-building' => 'Building',
            'bi bi-shield-check' => 'Security',
            'bi bi-tree' => 'Environment',
            'bi bi-car-front' => 'Parking',
            'bi bi-wifi' => 'WiFi',
            'bi bi-water' => 'Swimming Pool',
            'bi bi-dumbbell' => 'Gym',
            'bi bi-flower1' => 'Garden',
            'bi bi-camera' => 'CCTV',
            'bi bi-elevator' => 'Elevator',
            'bi bi-thermometer' => 'Air Conditioning',
            'bi bi-tools' => 'Maintenance',
            'bi bi-clock' => '24/7 Service',
            'bi bi-geo-alt' => 'Location',
            'bi bi-shop' => 'Shopping Center',
            'bi bi-mortarboard' => 'School',
            'bi bi-hospital' => 'Hospital',
            'bi bi-bus-front' => 'Transportation',
            'bi bi-signpost-2' => 'Access Road',
            'bi bi-lightning-charge' => 'Power Supply',
            'bi bi-droplet' => 'Water Supply',
            'bi bi-telephone' => 'Telephone',
            'bi bi-trash' => 'Waste Management',
            'bi bi-fire' => 'Fire Safety',
            'bi bi-people' => 'Community',
            'bi bi-cup-hot' => 'Cafe',
            'bi bi-bookmark-star' => 'Premium Quality',
            'bi bi-heart' => 'Family Friendly',
            'bi bi-star' => 'Luxury',
            'bi bi-gem' => 'Premium',
            'bi bi-award' => 'Award Winning',
            'bi bi-trophy' => 'Best Quality',
            'bi bi-check-circle' => 'Certified',
            'bi bi-umbrella' => 'Weather Protection',
            'bi bi-sun' => 'Natural Light',
            'bi bi-moon' => 'Night Security',
            'bi bi-brightness-high' => 'Bright Environment',
            'bi bi-wind' => 'Fresh Air',
            'bi bi-snow' => 'Cool Environment',
            'bi bi-thermometer-sun' => 'Climate Control',
            'bi bi-globe' => 'International Standard',
            'bi bi-graph-up' => 'Investment Value',
            'bi bi-currency-dollar' => 'Affordable',
            'bi bi-credit-card' => 'Easy Payment',
            'bi bi-bank' => 'Banking Facility',
            'bi bi-briefcase' => 'Business District',
            'bi bi-laptop' => 'Smart Home',
            'bi bi-router' => 'Internet Ready',
            'bi bi-phone' => 'Communication',
            'bi bi-headset' => 'Customer Service'
        ];
    }
}