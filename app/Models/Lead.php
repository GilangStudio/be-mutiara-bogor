<?php

namespace App\Models;

use App\Enum\LeadStatus;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function historyLead()
    {
        return $this->hasOne(HistoryLeads::class, 'leads_id');
    }

    public function historyLeads()
    {
        return $this->hasMany(HistoryLeads::class, 'leads_id');
    }

    public function historyMoveLeads()
    {
        return $this->hasMany(HistoryMoveLeads::class, 'leads_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeNewStatus($query)
    {
        return $query->where('status', LeadStatus::NEW->value);
    }

    public function scopeProcessStatus($query)
    {
        return $query->where('status', LeadStatus::PROCESS->value);
    }

    public function scopeClosingStatus($query)
    {
        return $query->where('status', LeadStatus::CLOSING->value);
    }

    public function scopeByPlatform($query, $platformId)
    {
        return $query->where('platform_id', $platformId);
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

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtoupper($value);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            LeadStatus::NEW->value => 'New',
            LeadStatus::PROCESS->value => 'Process',
            LeadStatus::CLOSING->value => 'Closing',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeColorAttribute()
    {
        return LeadStatus::color($this->status);
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

    public function getCurrentSalesAttribute()
    {
        return $this->historyLead?->sales;
    }

    // Static methods for statistics
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

    public static function getNewLeadsCount()
    {
        return self::newStatus()->count();
    }

    public static function getProcessLeadsCount()
    {
        return self::processStatus()->count();
    }

    public static function getClosingLeadsCount()
    {
        return self::closingStatus()->count();
    }
}