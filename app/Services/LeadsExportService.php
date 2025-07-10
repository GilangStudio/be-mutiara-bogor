<?php

namespace App\Services;

use App\Models\Lead;
use App\Enum\LeadStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeadsExportService
{
    /**
     * Export leads to CSV format
     */
    public function exportToCsv($filters = [])
    {
        $leads = $this->getFilteredLeads($filters);
        $csvData = $this->prepareCsvData($leads);
        
        $filename = 'leads_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, [
                'No',
                'Nama',
                'Telepon',
                'Email',
                'Platform',
                'Referral Path',
                'Sales',
                'Assignment Type',
                'Status',
                'Pesan',
                'Catatan',
                'Recontact Count',
                'Last Contact',
                'Tanggal Dibuat',
                'Tanggal Update',
                'Dibuat Oleh'
            ]);
            
            // Add data
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export leads to Excel format (using proper CSV with Excel-friendly formatting)
     */
    public function exportToExcel($filters = [])
    {
        $leads = $this->getFilteredLeads($filters);
        $csvData = $this->prepareCsvData($leads);
        
        $filename = 'leads_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Use CSV format with Excel-friendly headers
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function() use ($csvData) {
            // Create a simple CSV that Excel can open as XLSX
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers with semicolon separator for Excel compatibility
            $headers = [
                'No', 'Nama', 'Telepon', 'Email', 'Platform', 'Referral Path',
                'Sales', 'Assignment Type', 'Status', 'Pesan', 'Catatan',
                'Recontact Count', 'Last Contact', 'Tanggal Dibuat', 'Tanggal Update', 'Dibuat Oleh'
            ];
            
            // Use semicolon as separator for better Excel compatibility
            fwrite($output, implode(';', $headers) . "\r\n");
            
            // Add data rows
            foreach ($csvData as $row) {
                // Escape and format data for Excel
                $formattedRow = array_map(function($cell) {
                    // Handle special characters and wrap in quotes if needed
                    $cell = str_replace('"', '""', $cell); // Escape quotes
                    if (strpos($cell, ';') !== false || strpos($cell, '"') !== false || strpos($cell, "\n") !== false) {
                        return '"' . $cell . '"';
                    }
                    return $cell;
                }, $row);
                
                fwrite($output, implode(';', $formattedRow) . "\r\n");
            }
            
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Alternative Excel export using HTML table format (more compatible)
     */
    public function exportToExcelHtml($filters = [])
    {
        $leads = $this->getFilteredLeads($filters);
        $csvData = $this->prepareCsvData($leads);
        
        $filename = 'leads_export_' . now()->format('Y-m-d_H-i-s') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function() use ($csvData) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<meta name="ProgId" content="Excel.Sheet">';
            echo '<meta name="Generator" content="Microsoft Excel 11">';
            echo '<style>';
            echo 'table { border-collapse: collapse; }';
            echo 'th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }';
            echo 'th { background-color: #f0f0f0; font-weight: bold; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<table>';
            
            // Headers
            echo '<tr>';
            $headers = [
                'No', 'Nama', 'Telepon', 'Email', 'Platform', 'Referral Path',
                'Sales', 'Assignment Type', 'Status', 'Pesan', 'Catatan',
                'Recontact Count', 'Last Contact', 'Tanggal Dibuat', 'Tanggal Update', 'Dibuat Oleh'
            ];
            foreach ($headers as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr>';
            
            // Data rows
            foreach ($csvData as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . htmlspecialchars($cell) . '</td>';
                }
                echo '</tr>';
            }
            
            echo '</table>';
            echo '</body>';
            echo '</html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get filtered leads based on request parameters
     */
    private function getFilteredLeads($filters)
    {
        $query = Lead::with(['platform:id,platform_name', 'historyLead.sales', 'createdBy:id,name']);
        
        // Apply filters
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('message', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('note', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('path_referral', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['platform_id'])) {
            $query->where('platform_id', $filters['platform_id']);
        }
        
        if (!empty($filters['sales_id'])) {
            $query->whereHas('historyLead', function($q) use ($filters) {
                $q->where('sales_id', $filters['sales_id']);
            });
        }
        
        if (!empty($filters['assignment_type'])) {
            $assignmentType = $filters['assignment_type'] === 'auto';
            $query->whereHas('historyLead', function($q) use ($assignmentType) {
                $q->where('is_automatic', $assignmentType);
            });
        }

        if (!empty($filters['has_recontact'])) {
            if ($filters['has_recontact'] === 'true') {
                $query->where('recontact_count', '>', 0);
            } else {
                $query->where('recontact_count', '=', 0);
            }
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Prepare data for CSV export
     */
    private function prepareCsvData(Collection $leads)
    {
        $data = [];
        
        foreach ($leads as $index => $lead) {
            $data[] = [
                $index + 1, // No
                $lead->name,
                $lead->phone ?? '',
                $lead->email ?? '',
                $lead->platform->platform_name ?? '',
                $lead->path_referral ?? '',
                $lead->historyLead && $lead->historyLead->sales ? $lead->historyLead->sales->name : 'Belum assigned',
                $lead->historyLead ? ($lead->historyLead->is_automatic ? 'Automatic' : 'Manual') : '',
                match($lead->status) {
                    'NEW' => 'Baru',
                    'PROCESS' => 'Proses',
                    'CLOSING' => 'Closing',
                    default => $lead->status
                },
                $lead->message ?? '',
                $lead->note ?? '',
                $lead->recontact_count,
                $lead->last_contact_at ? $lead->last_contact_at->format('d M Y, H:i') : '',
                $lead->created_at->format('d M Y, H:i'),
                $lead->updated_at->format('d M Y, H:i'),
                $lead->createdBy->name ?? ''
            ];
        }
        
        return $data;
    }

    /**
     * Get export statistics
     */
    public function getExportStats($filters = [])
    {
        $leads = $this->getFilteredLeads($filters);
        
        $stats = [
            'total_leads' => $leads->count(),
            'status_breakdown' => [],
            'platform_breakdown' => [],
            'date_range' => [
                'from' => $leads->min('created_at')?->format('d M Y'),
                'to' => $leads->max('created_at')?->format('d M Y')
            ]
        ];

        // Status breakdown
        foreach (LeadStatus::cases() as $status) {
            $count = $leads->where('status', $status->value)->count();
            $stats['status_breakdown'][] = [
                'status' => match($status->value) {
                    'NEW' => 'Baru',
                    'PROCESS' => 'Proses',
                    'CLOSING' => 'Closing',
                    default => $status->value
                },
                'count' => $count
            ];
        }

        // Platform breakdown
        $platformCounts = $leads->groupBy('platform.platform_name')->map->count();
        foreach ($platformCounts as $platform => $count) {
            $stats['platform_breakdown'][] = [
                'platform' => $platform,
                'count' => $count
            ];
        }

        return $stats;
    }
}