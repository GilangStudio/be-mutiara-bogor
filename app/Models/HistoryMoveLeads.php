<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryMoveLeads extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'leads_id');
    }

    public function fromSales()
    {
        return $this->belongsTo(Sales::class, 'from_sales_id');
    }

    public function toSales()
    {
        return $this->belongsTo(Sales::class, 'to_sales_id');
    }

    // Scopes
    public function scopeByLead($query, $leadId)
    {
        return $query->where('leads_id', $leadId);
    }

    public function scopeFromSales($query, $salesId)
    {
        return $query->where('from_sales_id', $salesId);
    }

    public function scopeToSales($query, $salesId)
    {
        return $query->where('to_sales_id', $salesId);
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
}