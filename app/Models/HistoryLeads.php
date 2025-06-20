<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryLeads extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_automatic' => 'boolean',
        'is_favorited' => 'boolean'
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'leads_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    // Scopes
    public function scopeAutomatic($query)
    {
        return $query->where('is_automatic', true);
    }

    public function scopeManual($query)
    {
        return $query->where('is_automatic', false);
    }

    public function scopeFavorited($query)
    {
        return $query->where('is_favorited', true);
    }

    public function scopeBySales($query, $salesId)
    {
        return $query->where('sales_id', $salesId);
    }

    // Accessors
    public function getAssignmentTypeTextAttribute()
    {
        return $this->is_automatic ? 'Automatic' : 'Manual';
    }

    public function getAssignmentTypeBadgeColorAttribute()
    {
        return $this->is_automatic ? 'blue' : 'green';
    }
}