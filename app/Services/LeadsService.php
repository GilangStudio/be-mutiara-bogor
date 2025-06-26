<?php

namespace App\Services;

use App\Models\Sales;
use App\Models\HistoryLeads;

class LeadsService
{
 /**
     * Get next sales ID for automatic assignment
     */
    public function getNextSalesId()
    {
        $maxOrder = Sales::where('is_active', true)->max('order');
        
        if (!$maxOrder) {
            throw new \Exception('No active sales available');
        }

        $lastAutoAssignment = HistoryLeads::with('sales')
                                        ->where('is_automatic', true)
                                        ->orderBy('id', 'desc')
                                        ->first();

        if (!$lastAutoAssignment) {
            // First time assignment, get sales with order 1
            return Sales::where('is_active', true)
                       ->where('order', 1)
                       ->firstOrFail()
                       ->id;
        }

        $nextOrder = $lastAutoAssignment->sales->order + 1;

        if ($nextOrder > $maxOrder) {
            // Reset to first sales
            $nextOrder = 1;
        }

        return Sales::where('is_active', true)
                   ->where('order', $nextOrder)
                   ->firstOrFail()
                   ->id;
    }
}