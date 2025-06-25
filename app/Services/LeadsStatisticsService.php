<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Sales;
use App\Enum\LeadStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadsStatisticsService
{
    /**
     * Mendapatkan statistik lengkap untuk dashboard sales
     */
    public function getDashboardStats($salesId, array $options = [])
    {
        $period = $options['period'] ?? 'month';
        $platformId = $options['platform_id'] ?? null;
        $dateFrom = $options['date_from'] ?? null;
        $dateTo = $options['date_to'] ?? null;

        $dateRange = $this->getDateRange($period, $dateFrom, $dateTo);
        $baseQuery = $this->getBaseQuery($salesId, $platformId);

        return [
            'period_info' => [
                'period' => $period,
                'date_from' => $dateRange['from']->format('Y-m-d'),
                'date_to' => $dateRange['to']->format('Y-m-d'),
                'days_count' => $dateRange['from']->diffInDays($dateRange['to']) + 1
            ],
            'total_stats' => $this->getTotalLeadsStats($baseQuery, $dateRange),
            'status_distribution' => $this->getStatusDistribution($baseQuery, $dateRange),
            'platform_distribution' => $this->getPlatformDistribution($baseQuery, $dateRange),
            'trends' => $this->getLeadsTrends($baseQuery, $dateRange, $period),
            'assignment_stats' => $this->getAssignmentStats($salesId, $dateRange),
            'performance_metrics' => $this->getPerformanceMetrics($baseQuery, $dateRange),
            'recent_activities' => $this->getRecentActivities($salesId),
            'goals' => $this->getGoalsData($this->getTotalLeadsStats($baseQuery, $dateRange), $period)
        ];
    }

    /**
     * Mendapatkan base query untuk leads milik sales
     */
    private function getBaseQuery($salesId, $platformId = null)
    {
        $query = Lead::whereHas('historyLead', function ($q) use ($salesId) {
            $q->where('sales_id', $salesId);
        });

        if ($platformId) {
            $query->where('platform_id', $platformId);
        }

        return $query;
    }

    /**
     * Mendapatkan range tanggal berdasarkan periode
     */
    private function getDateRange($period, $dateFrom = null, $dateTo = null)
    {
        $now = now();
        
        return match($period) {
            'today' => [
                'from' => $now->copy()->startOfDay(),
                'to' => $now->copy()->endOfDay()
            ],
            'week' => [
                'from' => $now->copy()->startOfWeek(),
                'to' => $now->copy()->endOfWeek()
            ],
            'month' => [
                'from' => $now->copy()->startOfMonth(),
                'to' => $now->copy()->endOfMonth()
            ],
            'quarter' => [
                'from' => $now->copy()->startOfQuarter(),
                'to' => $now->copy()->endOfQuarter()
            ],
            'year' => [
                'from' => $now->copy()->startOfYear(),
                'to' => $now->copy()->endOfYear()
            ],
            'custom' => [
                'from' => $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : $now->copy()->startOfMonth(),
                'to' => $dateTo ? Carbon::parse($dateTo)->endOfDay() : $now->copy()->endOfMonth()
            ],
            default => [
                'from' => $now->copy()->startOfMonth(),
                'to' => $now->copy()->endOfMonth()
            ]
        };
    }

    /**
     * Mendapatkan statistik total leads dengan perbandingan periode sebelumnya
     */
    public function getTotalLeadsStats($baseQuery, $dateRange)
    {
        $currentPeriod = (clone $baseQuery)
            ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);

        // Periode sebelumnya untuk perbandingan
        $daysDiff = $dateRange['from']->diffInDays($dateRange['to']) + 1;
        $previousFrom = $dateRange['from']->copy()->subDays($daysDiff);
        $previousTo = $dateRange['from']->copy()->subDay();

        $previousPeriod = (clone $baseQuery)
            ->whereBetween('created_at', [$previousFrom, $previousTo]);

        $currentTotal = $currentPeriod->count();
        $previousTotal = $previousPeriod->count();

        $changePercentage = $previousTotal > 0 
            ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1)
            : ($currentTotal > 0 ? 100 : 0);

        return [
            'current_period' => $currentTotal,
            'previous_period' => $previousTotal,
            'change_percentage' => $changePercentage,
            'change_direction' => $changePercentage > 0 ? 'increase' : ($changePercentage < 0 ? 'decrease' : 'same'),
            'all_time_total' => (clone $baseQuery)->count()
        ];
    }

    /**
     * Mendapatkan distribusi status leads
     */
    public function getStatusDistribution($baseQuery, $dateRange)
    {
        $distribution = (clone $baseQuery)
            ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])
            ->select('status')
            ->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->get()
            ->keyBy('status');

        $total = $distribution->sum('count');

        $result = [];
        foreach (LeadStatus::cases() as $status) {
            $count = $distribution->get($status->value)?->count ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;

            $result[] = [
                'status' => $status->value,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return $result;
    }
    
    /**
     * Mendapatkan distribusi platform
     */
    public function getPlatformDistribution($baseQuery, $dateRange)
    {
        $distribution = (clone $baseQuery)
            ->whereBetween('leads.created_at', [$dateRange['from'], $dateRange['to']])
            ->join('platforms', 'leads.platform_id', '=', 'platforms.id')
            ->select('platforms.id', 'platforms.platform_name')
            ->groupBy('platforms.id', 'platforms.platform_name')
            ->selectRaw('platforms.id, platforms.platform_name, count(*) as count')
            ->orderBy('count', 'desc')
            ->get();

        $total = $distribution->sum('count');

        return $distribution->map(function ($item) use ($total) {
            $percentage = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
            
            return [
                'platform_id' => $item->id,
                'platform_name' => $item->platform_name,
                'count' => $item->count,
                'percentage' => $percentage
            ];
        })->toArray();
    }

    /**
     * Mendapatkan trend leads berdasarkan periode
     */
    public function getLeadsTrends($baseQuery, $dateRange, $period)
    {
        $query = (clone $baseQuery)
            ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);

        // Tentukan format grouping berdasarkan periode
        $dateFormat = match($period) {
            'today' => '%H:00',
            'week' => '%Y-%m-%d',
            'month' => '%Y-%m-%d',
            'quarter' => '%Y-%u',
            'year' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $trends = $query
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as date_group")
            ->selectRaw('count(*) as count')
            ->groupBy('date_group')
            ->orderBy('date_group')
            ->get();

        return $trends->map(function ($item) use ($period) {
            return [
                'date' => $item->date_group,
                'count' => $item->count,
                'formatted_date' => $this->formatTrendDate($item->date_group, $period)
            ];
        })->toArray();
    }

    /**
     * Mendapatkan statistik assignment type
     */
    public function getAssignmentStats($salesId, $dateRange)
    {
        $stats = DB::table('history_leads')
            ->join('leads', 'history_leads.leads_id', '=', 'leads.id')
            ->where('history_leads.sales_id', $salesId)
            ->whereBetween('leads.created_at', [$dateRange['from'], $dateRange['to']])
            ->select('history_leads.is_automatic')
            ->groupBy('history_leads.is_automatic')
            ->selectRaw('is_automatic, count(*) as count')
            ->get()
            ->keyBy('is_automatic');

        $autoCount = $stats->get(1)?->count ?? 0;
        $manualCount = $stats->get(0)?->count ?? 0;
        $total = $autoCount + $manualCount;

        return [
            'automatic' => [
                'count' => $autoCount,
                'percentage' => $total > 0 ? round(($autoCount / $total) * 100, 1) : 0
            ],
            'manual' => [
                'count' => $manualCount,
                'percentage' => $total > 0 ? round(($manualCount / $total) * 100, 1) : 0
            ],
            'total' => $total
        ];
    }

    /**
     * Mendapatkan performance metrics
     */
    public function getPerformanceMetrics($baseQuery, $dateRange)
    {
        $leads = (clone $baseQuery)
            ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])
            ->get();

        $totalLeads = $leads->count();
        $closingLeads = $leads->where('status', LeadStatus::CLOSING->value)->count();
        $processLeads = $leads->where('status', LeadStatus::PROCESS->value)->count();

        $conversionRate = $totalLeads > 0 ? round(($closingLeads / $totalLeads) * 100, 1) : 0;
        $processRate = $totalLeads > 0 ? round((($processLeads + $closingLeads) / $totalLeads) * 100, 1) : 0;

        $avgResponseTime = $this->calculateAverageResponseTime($leads);

        return [
            'conversion_rate' => [
                'value' => $conversionRate,
                'description' => 'Persentase leads yang closing'
            ],
            'process_rate' => [
                'value' => $processRate,
                'description' => 'Persentase leads yang diproses'
            ],
            'avg_response_time' => [
                'value' => $avgResponseTime,
                'description' => 'Rata-rata waktu respon (jam)'
            ],
            'productivity_score' => [
                'value' => min(100, ($conversionRate * 0.6) + ($processRate * 0.4)),
                'description' => 'Skor produktivitas keseluruhan'
            ]
        ];
    }

    /**
     * Mendapatkan aktivitas terbaru
     */
    public function getRecentActivities($salesId, $limit = 5)
    {
        $recentLeads = Lead::whereHas('historyLead', function ($query) use ($salesId) {
                $query->where('sales_id', $salesId);
            })
            ->with(['platform:id,platform_name'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        return $recentLeads->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'leads_status' => $lead->status,
                'platform_name' => $lead->platform->platform_name,
                'updated_at' => $lead->updated_at->format('d M Y, H:i'),
                'updated_diff' => $lead->updated_at->diffForHumans()
            ];
        })->toArray();
    }

    /**
     * Mendapatkan data goals dan target
     */
    public function getGoalsData($totalStats, $period)
    {
        $targets = [
            'today' => 5,
            'week' => 20,
            'month' => 80,
            'quarter' => 240,
            'year' => 960
        ];

        $target = $targets[$period] ?? 80;
        $current = $totalStats['current_period'];
        $achievement = $target > 0 ? round(($current / $target) * 100, 1) : 0;

        return [
            'target' => $target,
            'current' => $current,
            'achievement_percentage' => $achievement,
            'remaining' => max(0, $target - $current),
            'status' => $achievement >= 100 ? 'achieved' : ($achievement >= 80 ? 'on_track' : 'behind')
        ];
    }

    private function formatTrendDate($dateGroup, $period)
    {
        return match($period) {
            'today' => $dateGroup,
            'week', 'month' => Carbon::parse($dateGroup)->format('d M'),
            'quarter' => "Week {$dateGroup}",
            'year' => Carbon::parse($dateGroup . '-01')->format('M Y'),
            default => $dateGroup
        };
    }

    private function calculateAverageResponseTime($leads)
    {
        if ($leads->isEmpty()) return 0;

        $totalHours = 0;
        $count = 0;

        foreach ($leads as $lead) {
            if ($lead->updated_at > $lead->created_at) {
                $totalHours += $lead->created_at->diffInHours($lead->updated_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalHours / $count, 1) : 0;
    }
}