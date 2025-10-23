<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class ReportAnalysisService
{
    /**
     * Analyze report content using AI-like pattern matching
     */
    public function analyzeReportContent(string $reason, string $details): array
    {
        $content = strtolower($reason . ' ' . $details);
        
        // Spam detection patterns
        $spamPatterns = [
            'spam', 'promotional', 'advertisement', 'buy now', 'click here',
            'limited offer', 'free money', 'get rich', 'make money fast'
        ];
        
        // Inappropriate content patterns
        $inappropriatePatterns = [
            'inappropriate', 'offensive', 'vulgar', 'explicit', 'nsfw',
            'adult content', 'pornographic', 'sexual'
        ];
        
        // Fraud patterns
        $fraudPatterns = [
            'fraud', 'scam', 'fake', 'phishing', 'steal', 'cheat',
            'money laundering', 'ponzi', 'pyramid scheme', 'deceptive'
        ];
        
        // Harassment patterns
        $harassmentPatterns = [
            'harassment', 'bullying', 'threatening', 'stalking', 'intimidation',
            'abuse', 'hate speech', 'discrimination', 'racist', 'sexist'
        ];
        
        // Violence patterns
        $violencePatterns = [
            'violence', 'violent', 'attack', 'assault', 'threat', 'weapon',
            'dangerous', 'harm', 'kill', 'death threat'
        ];
        
        // Calculate scores
        $scores = [
            'spam' => $this->calculatePatternScore($content, $spamPatterns),
            'inappropriate' => $this->calculatePatternScore($content, $inappropriatePatterns),
            'fraud' => $this->calculatePatternScore($content, $fraudPatterns),
            'harassment' => $this->calculatePatternScore($content, $harassmentPatterns),
            'violence' => $this->calculatePatternScore($content, $violencePatterns),
        ];
        
        // Determine suggested category
        $suggestedCategory = array_keys($scores, max($scores))[0];
        $confidence = max($scores);
        
        // Determine priority based on content severity
        $priority = $this->determinePriority($scores, $content);
        
        // Calculate risk score (0-100)
        $riskScore = $this->calculateRiskScore($scores, $content);
        
        return [
            'suggested_category' => $suggestedCategory,
            'confidence' => round($confidence * 100, 2),
            'priority' => $priority,
            'risk_score' => $riskScore,
            'category_scores' => array_map(fn($s) => round($s * 100, 2), $scores),
            'requires_immediate_attention' => $riskScore >= 80,
            'auto_flag' => $this->shouldAutoFlag($scores, $riskScore),
        ];
    }
    
    /**
     * Calculate pattern matching score
     */
    private function calculatePatternScore(string $content, array $patterns): float
    {
        $matches = 0;
        $totalPatterns = count($patterns);
        
        foreach ($patterns as $pattern) {
            if (str_contains($content, $pattern)) {
                $matches++;
            }
        }
        
        return $totalPatterns > 0 ? $matches / $totalPatterns : 0;
    }
    
    /**
     * Determine priority based on scores
     */
    private function determinePriority(array $scores, string $content): string
    {
        $maxScore = max($scores);
        $wordCount = str_word_count($content);
        
        // Critical: High score + violence/fraud
        if ($maxScore >= 0.4 && ($scores['violence'] >= 0.3 || $scores['fraud'] >= 0.4)) {
            return 'critical';
        }
        
        // High: Moderate to high scores
        if ($maxScore >= 0.3 || $wordCount > 100) {
            return 'high';
        }
        
        // Medium: Low to moderate scores
        if ($maxScore >= 0.15) {
            return 'medium';
        }
        
        return 'low';
    }
    
    /**
     * Calculate overall risk score
     */
    private function calculateRiskScore(array $scores, string $content): int
    {
        $baseScore = max($scores) * 100;
        
        // Boost for multiple categories
        $categoriesDetected = count(array_filter($scores, fn($s) => $s > 0.1));
        $multiCategoryBoost = ($categoriesDetected - 1) * 10;
        
        // Boost for urgency words
        $urgencyWords = ['urgent', 'immediate', 'emergency', 'asap', 'critical'];
        $urgencyBoost = 0;
        foreach ($urgencyWords as $word) {
            if (str_contains($content, $word)) {
                $urgencyBoost += 5;
            }
        }
        
        $totalScore = min(100, $baseScore + $multiCategoryBoost + $urgencyBoost);
        
        return (int) $totalScore;
    }
    
    /**
     * Determine if report should be auto-flagged
     */
    private function shouldAutoFlag(array $scores, int $riskScore): bool
    {
        // Auto-flag if violence or fraud score is high
        if ($scores['violence'] >= 0.4 || $scores['fraud'] >= 0.5) {
            return true;
        }
        
        // Auto-flag if overall risk is very high
        if ($riskScore >= 85) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get organization risk profile
     */
    public function getOrganizationRiskProfile(Organization $organization): array
    {
        $reports = $organization->reports;
        
        if ($reports->isEmpty()) {
            return [
                'risk_level' => 'low',
                'total_reports' => 0,
                'open_reports' => 0,
                'average_severity' => 0,
                'recommendation' => 'No reports filed',
            ];
        }
        
        $totalReports = $reports->count();
        $openReports = $reports->where('status', 'open')->count();
        $criticalReports = $reports->where('priority', 'critical')->count();
        $highReports = $reports->where('priority', 'high')->count();
        
        // Calculate average severity
        $severityMap = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        $totalSeverity = $reports->sum(fn($r) => $severityMap[$r->priority] ?? 1);
        $averageSeverity = $totalSeverity / $totalReports;
        
        // Determine risk level
        $riskLevel = 'low';
        $recommendation = 'Continue monitoring';
        
        if ($criticalReports >= 2 || $openReports >= 5) {
            $riskLevel = 'critical';
            $recommendation = 'Immediate action required - Consider suspension';
        } elseif ($highReports >= 3 || $openReports >= 3) {
            $riskLevel = 'high';
            $recommendation = 'Review and take action soon';
        } elseif ($totalReports >= 5 || $averageSeverity >= 2.5) {
            $riskLevel = 'medium';
            $recommendation = 'Monitor closely';
        }
        
        return [
            'risk_level' => $riskLevel,
            'total_reports' => $totalReports,
            'open_reports' => $openReports,
            'critical_reports' => $criticalReports,
            'high_reports' => $highReports,
            'average_severity' => round($averageSeverity, 2),
            'recommendation' => $recommendation,
            'should_suspend' => $riskLevel === 'critical',
        ];
    }
    
    /**
     * Get advanced statistics
     */
    public function getAdvancedStatistics(): array
    {
        $now = now();
        $lastWeek = $now->copy()->subWeek();
        $lastMonth = $now->copy()->subMonth();
        
        return [
            'overview' => [
                'total' => Report::count(),
                'open' => Report::where('status', 'open')->count(),
                'in_review' => Report::where('status', 'in_review')->count(),
                'resolved' => Report::where('status', 'resolved')->count(),
                'dismissed' => Report::where('status', 'dismissed')->count(),
            ],
            'by_priority' => [
                'critical' => Report::where('priority', 'critical')->count(),
                'high' => Report::where('priority', 'high')->count(),
                'medium' => Report::where('priority', 'medium')->count(),
                'low' => Report::where('priority', 'low')->count(),
            ],
            'by_category' => Report::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
            'trends' => [
                'last_week' => Report::where('created_at', '>=', $lastWeek)->count(),
                'last_month' => Report::where('created_at', '>=', $lastMonth)->count(),
                'resolved_last_week' => Report::where('status', 'resolved')
                    ->where('resolved_at', '>=', $lastWeek)->count(),
            ],
            'response_time' => $this->calculateAverageResponseTime(),
            'top_reported_organizations' => $this->getTopReportedOrganizations(),
            'resolution_rate' => $this->calculateResolutionRate(),
        ];
    }
    
    /**
     * Calculate average response time
     */
    private function calculateAverageResponseTime(): array
    {
        $resolvedReports = Report::whereNotNull('resolved_at')->get();
        
        if ($resolvedReports->isEmpty()) {
            return [
                'average_hours' => 0,
                'average_days' => 0,
            ];
        }
        
        $totalHours = 0;
        foreach ($resolvedReports as $report) {
            $hours = $report->created_at->diffInHours($report->resolved_at);
            $totalHours += $hours;
        }
        
        $averageHours = $totalHours / $resolvedReports->count();
        
        return [
            'average_hours' => round($averageHours, 2),
            'average_days' => round($averageHours / 24, 2),
        ];
    }
    
    /**
     * Get top reported organizations
     */
    private function getTopReportedOrganizations(int $limit = 5): array
    {
        return Report::select('organization_id', DB::raw('count(*) as report_count'))
            ->whereNotNull('organization_id')
            ->groupBy('organization_id')
            ->orderByDesc('report_count')
            ->limit($limit)
            ->with('organization:id,name')
            ->get()
            ->map(fn($r) => [
                'organization' => $r->organization?->name ?? 'N/A',
                'report_count' => $r->report_count,
            ])
            ->toArray();
    }
    
    /**
     * Calculate resolution rate
     */
    private function calculateResolutionRate(): array
    {
        $total = Report::count();
        $resolved = Report::where('status', 'resolved')->count();
        $dismissed = Report::where('status', 'dismissed')->count();
        
        $rate = $total > 0 ? (($resolved + $dismissed) / $total) * 100 : 0;
        
        return [
            'total' => $total,
            'resolved' => $resolved,
            'dismissed' => $dismissed,
            'rate' => round($rate, 2),
        ];
    }
    
    /**
     * Search reports with advanced filters
     */
    public function searchReports(array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $query = Report::with(['user', 'organization', 'event', 'latestAction']);
        
        // Text search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        
        // Priority filter
        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            $query->where('priority', $filters['priority']);
        }
        
        // Category filter
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }
        
        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        
        // Organization filter
        if (!empty($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }
        
        // Reporter filter
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        // Risk score filter (requires custom calculation)
        if (!empty($filters['min_risk_score'])) {
            // This would require a stored risk_score column or real-time calculation
        }
        
        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
        
        return $query;
    }
}
