<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportAction;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Services\ReportAnalysisService;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Get some users, organizations, and events
        $users = User::where('role', 'member')->take(5)->get();
        $admins = User::where('role', 'admin')->get();
        $organizations = Organization::take(3)->get();
        $events = Event::take(3)->get();

        if ($users->isEmpty() || $organizations->isEmpty()) {
            $this->command->warn('Skipping ReportSeeder: No users or organizations found');
            return;
        }

        $analysisService = new ReportAnalysisService();
        $categories = ['spam', 'inappropriate', 'fraud', 'harassment', 'violence', 'misinformation', 'copyright', 'other'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['open', 'in_review', 'resolved', 'dismissed'];

        // Create reports for organizations
        foreach ($organizations as $org) {
            $reportCount = rand(2, 5);
            
            for ($i = 0; $i < $reportCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $category = $categories[array_rand($categories)];
                $priority = $priorities[array_rand($priorities)];
                
                $reason = $this->getReasonForCategory($category);
                $details = $this->getDetailsForCategory($category);
                
                // AI Analysis
                $aiAnalysis = $analysisService->analyzeReportContent($reason, $details);
                
                $report = Report::create([
                    'user_id' => $users->random()->id,
                    'organization_id' => $org->id,
                    'event_id' => null,
                    'category' => $category,
                    'priority' => $priority,
                    'reason' => $reason,
                    'details' => $details,
                    'status' => $status,
                    'resolved_at' => in_array($status, ['resolved', 'dismissed']) ? now()->subDays(rand(1, 10)) : null,
                    'resolved_by' => in_array($status, ['resolved', 'dismissed']) && $admins->isNotEmpty() ? $admins->random()->id : null,
                    'ai_risk_score' => $aiAnalysis['risk_score'],
                    'ai_suggested_category' => $aiAnalysis['suggested_category'],
                    'ai_confidence' => $aiAnalysis['confidence'],
                    'ai_auto_flagged' => $aiAnalysis['auto_flag'],
                    'ai_analysis' => $aiAnalysis,
                ]);

                // Add actions to the report
                if (in_array($status, ['in_review', 'resolved', 'dismissed'])) {
                    $this->createReportActions($report, $admins, $status);
                }
            }
        }

        // Create reports for events
        foreach ($events as $event) {
            if (rand(0, 1)) { // 50% chance
                $status = $statuses[array_rand($statuses)];
                $category = $categories[array_rand($categories)];
                $priority = $priorities[array_rand($priorities)];
                
                $reason = $this->getReasonForCategory($category);
                $details = $this->getDetailsForCategory($category);
                
                // AI Analysis
                $aiAnalysis = $analysisService->analyzeReportContent($reason, $details);
                
                $report = Report::create([
                    'user_id' => $users->random()->id,
                    'organization_id' => null,
                    'event_id' => $event->id,
                    'category' => $category,
                    'priority' => $priority,
                    'reason' => $reason,
                    'details' => $details,
                    'status' => $status,
                    'resolved_at' => in_array($status, ['resolved', 'dismissed']) ? now()->subDays(rand(1, 10)) : null,
                    'resolved_by' => in_array($status, ['resolved', 'dismissed']) && $admins->isNotEmpty() ? $admins->random()->id : null,
                    'ai_risk_score' => $aiAnalysis['risk_score'],
                    'ai_suggested_category' => $aiAnalysis['suggested_category'],
                    'ai_confidence' => $aiAnalysis['confidence'],
                    'ai_auto_flagged' => $aiAnalysis['auto_flag'],
                    'ai_analysis' => $aiAnalysis,
                ]);

                // Add actions to the report
                if (in_array($status, ['in_review', 'resolved', 'dismissed'])) {
                    $this->createReportActions($report, $admins, $status);
                }
            }
        }

        $this->command->info('Reports and Report Actions seeded successfully!');
    }

    private function createReportActions($report, $admins, $status)
    {
        if ($admins->isEmpty()) {
            return;
        }

        $actionTypes = ['reviewed', 'investigating', 'warning_sent', 'content_removed', 'account_suspended'];
        $actionCount = rand(1, 3);

        for ($i = 0; $i < $actionCount; $i++) {
            $actionType = $i === 0 ? 'reviewed' : $actionTypes[array_rand($actionTypes)];
            
            // Last action should match the report status
            if ($i === $actionCount - 1) {
                if ($status === 'resolved') {
                    $actionType = 'resolved';
                } elseif ($status === 'dismissed') {
                    $actionType = 'dismissed';
                }
            }

            ReportAction::create([
                'report_id' => $report->id,
                'admin_id' => $admins->random()->id,
                'action_type' => $actionType,
                'action_note' => $this->getActionNote($actionType),
                'internal_note' => 'Internal note: ' . $this->getActionNote($actionType),
                'action_taken_at' => now()->subDays(rand(0, 5))->subHours(rand(0, 23)),
            ]);
        }
    }

    private function getReasonForCategory($category)
    {
        $reasons = [
            'spam' => 'This organization is posting spam content repeatedly',
            'inappropriate' => 'Inappropriate content that violates community guidelines',
            'fraud' => 'Suspected fraudulent activity and misleading information',
            'harassment' => 'Harassing behavior towards community members',
            'violence' => 'Content promoting violence or harmful activities',
            'misinformation' => 'Spreading false or misleading information',
            'copyright' => 'Using copyrighted material without permission',
            'other' => 'Other concerns that need attention',
        ];

        return $reasons[$category] ?? 'Report submitted';
    }

    private function getDetailsForCategory($category)
    {
        $details = [
            'spam' => 'I have noticed multiple spam posts from this organization. They are posting promotional content that is not relevant to environmental causes. This needs to be reviewed.',
            'inappropriate' => 'The content shared by this organization contains inappropriate material that does not align with community standards. Please investigate.',
            'fraud' => 'This organization appears to be collecting donations under false pretenses. The events they claim to organize do not seem legitimate.',
            'harassment' => 'Members of this organization have been sending harassing messages to other users. This behavior is unacceptable.',
            'violence' => 'Some posts from this organization promote violent activities. This is concerning and should be addressed immediately.',
            'misinformation' => 'The organization is spreading false information about environmental issues. This could mislead community members.',
            'copyright' => 'They are using images and content that belong to other organizations without proper attribution or permission.',
            'other' => 'There are several concerns about this organization that need to be reviewed by the admin team.',
        ];

        return $details[$category] ?? 'Please review this report.';
    }

    private function getActionNote($actionType)
    {
        $notes = [
            'reviewed' => 'Report has been reviewed by our moderation team.',
            'investigating' => 'Currently investigating the reported issue.',
            'resolved' => 'Issue has been resolved. Appropriate action has been taken.',
            'dismissed' => 'After review, this report has been dismissed as it does not violate our guidelines.',
            'warning_sent' => 'A warning has been sent to the organization.',
            'content_removed' => 'Inappropriate content has been removed.',
            'account_suspended' => 'Account has been temporarily suspended pending further review.',
        ];

        return $notes[$actionType] ?? 'Action taken on this report.';
    }
}
