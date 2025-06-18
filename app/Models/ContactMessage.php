<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'replied_at' => 'datetime'
    ];

    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'unread' => 'Unread',
            'read' => 'Read',
            'replied' => 'Replied'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'unread' => 'red',
            'read' => 'yellow',
            'replied' => 'green'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return null;
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        // Format Indonesian phone number
        if (substr($phone, 0, 1) === '0') {
            return '+62' . substr($phone, 1);
        }
        
        return $this->phone;
    }

    public function getWhatsappUrlAttribute()
    {
        if (!$this->phone) {
            return null;
        }

        $phone = $this->formatted_phone;
        return "https://wa.me/" . str_replace('+', '', $phone);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Static methods for statistics
    public static function getUnreadCount()
    {
        return self::unread()->count();
    }

    public static function getTodayCount()
    {
        return self::today()->count();
    }

    public static function getThisWeekCount()
    {
        return self::thisWeek()->count();
    }

    public static function getThisMonthCount()
    {
        return self::thisMonth()->count();
    }
}