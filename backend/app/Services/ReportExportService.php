<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

/**
 * Service for exporting reports in various formats
 */
class ReportExportService
{
    /**
     * Export reports to CSV
     */
    public function exportToCSV(Collection $reports): string
    {
        $csvData = [];
        
        // Headers
        $csvData[] = [
            'ID',
            'Date',
            'Status',
            'Priority',
            'Category',
            'Reported Item Type',
            'Reported Item',
            'Reporter Name',
            'Reporter Email',
            'Reason',
            'Details',
            'AI Risk Score',
            'AI Suggested Category',
            'AI Confidence',
            'AI Auto-Flagged',
            'Resolved At',
            'Resolved By',
        ];
        
        // Data rows
        foreach ($reports as $report) {
            $csvData[] = [
                $report->id,
                $report->created_at->format('Y-m-d H:i:s'),
                ucfirst($report->status),
                ucfirst($report->priority),
                $report->categoryLabel,
                $report->organization_id ? 'Organization' : 'Event',
                $report->organization_id 
                    ? ($report->organization->name ?? 'N/A')
                    : ($report->event->title ?? 'N/A'),
                $report->user->full_name ?? 'N/A',
                $report->user->email ?? 'N/A',
                $report->reason,
                $report->details ?? '',
                $report->ai_risk_score ?? 0,
                $report->ai_suggested_category ?? 'N/A',
                $report->ai_confidence ?? 0,
                $report->ai_auto_flagged ? 'Yes' : 'No',
                $report->resolved_at ? $report->resolved_at->format('Y-m-d H:i:s') : '',
                $report->resolver ? $report->resolver->full_name : '',
            ];
        }
        
        // Generate CSV content
        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
    
    /**
     * Export reports to Excel (HTML table format)
     */
    public function exportToExcel(Collection $reports): string
    {
        $html = '<html><head><meta charset="UTF-8">';
        $html .= '<style>
            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
            th { background-color: #4CAF50; color: white; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold; }
            td { padding: 10px; border: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .status-open { background-color: #fff3cd; }
            .status-resolved { background-color: #d4edda; }
            .status-dismissed { background-color: #f8d7da; }
            .priority-critical { color: #721c24; font-weight: bold; }
            .priority-high { color: #856404; font-weight: bold; }
        </style></head><body>';
        
        $html .= '<h1>Reports Export - ' . now()->format('Y-m-d H:i:s') . '</h1>';
        $html .= '<p>Total Reports: ' . $reports->count() . '</p>';
        
        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th>ID</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Priority</th>';
        $html .= '<th>Category</th>';
        $html .= '<th>Type</th>';
        $html .= '<th>Reported Item</th>';
        $html .= '<th>Reporter</th>';
        $html .= '<th>Reason</th>';
        $html .= '<th>AI Risk</th>';
        $html .= '<th>Resolved</th>';
        $html .= '</tr></thead><tbody>';
        
        foreach ($reports as $report) {
            $statusClass = 'status-' . $report->status;
            $priorityClass = 'priority-' . $report->priority;
            
            $html .= '<tr class="' . $statusClass . '">';
            $html .= '<td>' . $report->id . '</td>';
            $html .= '<td>' . $report->created_at->format('Y-m-d H:i') . '</td>';
            $html .= '<td>' . ucfirst($report->status) . '</td>';
            $html .= '<td class="' . $priorityClass . '">' . ucfirst($report->priority) . '</td>';
            $html .= '<td>' . $report->categoryLabel . '</td>';
            $html .= '<td>' . ($report->organization_id ? 'Organization' : 'Event') . '</td>';
            $html .= '<td>' . ($report->organization_id 
                ? ($report->organization->name ?? 'N/A')
                : ($report->event->title ?? 'N/A')) . '</td>';
            $html .= '<td>' . ($report->user->full_name ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($report->reason) . '</td>';
            $html .= '<td>' . ($report->ai_risk_score ?? 0) . '</td>';
            $html .= '<td>' . ($report->resolved_at ? $report->resolved_at->format('Y-m-d') : '-') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></body></html>';
        
        return $html;
    }
    
    /**
     * Generate PDF content (HTML format for PDF libraries)
     */
    public function generatePDFContent(Collection $reports, array $stats = []): string
    {
        return View::make('admin.reports.pdf', [
            'reports' => $reports,
            'stats' => $stats,
            'generatedAt' => now(),
        ])->render();
    }
    
    /**
     * Export single report to PDF
     */
    public function generateSingleReportPDF(Report $report): string
    {
        return View::make('admin.reports.single-pdf', [
            'report' => $report,
            'generatedAt' => now(),
        ])->render();
    }
    
    /**
     * Generate statistics summary
     */
    public function generateStatisticsSummary(Collection $reports): array
    {
        return [
            'total' => $reports->count(),
            'by_status' => [
                'open' => $reports->where('status', 'open')->count(),
                'in_review' => $reports->where('status', 'in_review')->count(),
                'resolved' => $reports->where('status', 'resolved')->count(),
                'dismissed' => $reports->where('status', 'dismissed')->count(),
            ],
            'by_priority' => [
                'critical' => $reports->where('priority', 'critical')->count(),
                'high' => $reports->where('priority', 'high')->count(),
                'medium' => $reports->where('priority', 'medium')->count(),
                'low' => $reports->where('priority', 'low')->count(),
            ],
            'by_category' => $reports->groupBy('category')->map->count()->toArray(),
            'ai_stats' => [
                'auto_flagged' => $reports->where('ai_auto_flagged', true)->count(),
                'high_risk' => $reports->where('ai_risk_score', '>=', 70)->count(),
                'average_risk' => round($reports->avg('ai_risk_score'), 2),
                'gemini_analyzed' => $reports->filter(function($r) {
                    return isset($r->ai_analysis['ai_powered']) && $r->ai_analysis['ai_powered'];
                })->count(),
            ],
            'time_stats' => [
                'oldest' => $reports->min('created_at'),
                'newest' => $reports->max('created_at'),
                'avg_resolution_time' => $this->calculateAverageResolutionTime($reports),
            ],
        ];
    }
    
    /**
     * Calculate average resolution time
     */
    protected function calculateAverageResolutionTime(Collection $reports): ?string
    {
        $resolved = $reports->whereNotNull('resolved_at');
        
        if ($resolved->isEmpty()) {
            return null;
        }
        
        $totalHours = 0;
        foreach ($resolved as $report) {
            $totalHours += $report->created_at->diffInHours($report->resolved_at);
        }
        
        $avgHours = $totalHours / $resolved->count();
        $days = floor($avgHours / 24);
        $hours = $avgHours % 24;
        
        if ($days > 0) {
            return sprintf('%d days, %d hours', $days, $hours);
        }
        
        return sprintf('%d hours', $hours);
    }
    
    /**
     * Export reports with filters to JSON
     */
    public function exportToJSON(Collection $reports): string
    {
        $data = [
            'export_date' => now()->toISOString(),
            'total_reports' => $reports->count(),
            'statistics' => $this->generateStatisticsSummary($reports),
            'reports' => $reports->map(function($report) {
                return [
                    'id' => $report->id,
                    'created_at' => $report->created_at->toISOString(),
                    'status' => $report->status,
                    'priority' => $report->priority,
                    'category' => $report->category,
                    'category_label' => $report->categoryLabel,
                    'reported_item' => [
                        'type' => $report->organization_id ? 'organization' : 'event',
                        'id' => $report->organization_id ?? $report->event_id,
                        'name' => $report->organization_id 
                            ? ($report->organization->name ?? null)
                            : ($report->event->title ?? null),
                    ],
                    'reporter' => [
                        'id' => $report->user_id,
                        'name' => $report->user->full_name ?? null,
                        'email' => $report->user->email ?? null,
                    ],
                    'content' => [
                        'reason' => $report->reason,
                        'details' => $report->details,
                    ],
                    'ai_analysis' => [
                        'risk_score' => $report->ai_risk_score,
                        'suggested_category' => $report->ai_suggested_category,
                        'confidence' => $report->ai_confidence,
                        'auto_flagged' => $report->ai_auto_flagged,
                        'powered_by_gemini' => $report->ai_analysis['ai_powered'] ?? false,
                        'summary' => $report->ai_analysis['analysis_summary'] ?? null,
                        'recommended_action' => $report->ai_analysis['recommended_action'] ?? null,
                    ],
                    'resolution' => [
                        'resolved_at' => $report->resolved_at?->toISOString(),
                        'resolved_by' => $report->resolver?->full_name,
                        'actions_count' => $report->actions->count(),
                    ],
                ];
            })->values(),
        ];
        
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Generate advanced analytics report
     */
    public function generateAnalyticsReport(Collection $reports): array
    {
        $stats = $this->generateStatisticsSummary($reports);
        
        // Trend analysis
        $byMonth = $reports->groupBy(function($report) {
            return $report->created_at->format('Y-m');
        })->map->count();
        
        // Top reporters
        $topReporters = $reports->groupBy('user_id')
            ->map(function($userReports) {
                $first = $userReports->first();
                return [
                    'name' => $first->user->full_name ?? 'N/A',
                    'email' => $first->user->email ?? 'N/A',
                    'count' => $userReports->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();
        
        // Most reported organizations
        $topOrganizations = $reports->whereNotNull('organization_id')
            ->groupBy('organization_id')
            ->map(function($orgReports) {
                $first = $orgReports->first();
                return [
                    'name' => $first->organization->name ?? 'N/A',
                    'count' => $orgReports->count(),
                    'categories' => $orgReports->pluck('category')->unique()->values(),
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();
        
        // AI Performance
        $aiPerformance = [
            'total_analyzed' => $reports->whereNotNull('ai_risk_score')->count(),
            'gemini_usage' => $stats['ai_stats']['gemini_analyzed'],
            'pattern_matching_usage' => $reports->whereNotNull('ai_risk_score')->count() - $stats['ai_stats']['gemini_analyzed'],
            'auto_flag_rate' => $reports->count() > 0 
                ? round(($stats['ai_stats']['auto_flagged'] / $reports->count()) * 100, 2)
                : 0,
            'high_risk_rate' => $reports->count() > 0
                ? round(($stats['ai_stats']['high_risk'] / $reports->count()) * 100, 2)
                : 0,
        ];
        
        return [
            'summary' => $stats,
            'trends' => [
                'by_month' => $byMonth,
            ],
            'top_reporters' => $topReporters,
            'top_reported_organizations' => $topOrganizations,
            'ai_performance' => $aiPerformance,
            'resolution_metrics' => [
                'resolution_rate' => $reports->count() > 0
                    ? round((($stats['by_status']['resolved'] + $stats['by_status']['dismissed']) / $reports->count()) * 100, 2)
                    : 0,
                'average_resolution_time' => $stats['time_stats']['avg_resolution_time'],
                'pending_reports' => $stats['by_status']['open'] + $stats['by_status']['in_review'],
            ],
        ];
    }
}
