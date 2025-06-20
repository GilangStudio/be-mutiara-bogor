<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function historyLeads()
    {
        return $this->hasMany(HistoryLeads::class, 'sales_id');
    }

    public function historyMoveLeadsFrom()
    {
        return $this->hasMany(HistoryMoveLeads::class, 'from_sales_id');
    }

    public function historyMoveLeadsTo()
    {
        return $this->hasMany(HistoryMoveLeads::class, 'to_sales_id');
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getStatusBadgeColorAttribute()
    {
        return $this->is_active ? 'green' : 'red';
    }

    public function getTotalLeadsAttribute()
    {
        return $this->historyLeads()->count();
    }

    public function getFormattedPhoneAttribute()
    {
        // Format phone number for WhatsApp
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        if (substr($phone, 0, 1) === '0') {
            return '+62' . substr($phone, 1);
        }
        
        return $this->phone;
    }

    public function getWhatsappUrlAttribute()
    {
        $phone = $this->formatted_phone;
        return "https://wa.me/" . str_replace('+', '', $phone);
    }
}