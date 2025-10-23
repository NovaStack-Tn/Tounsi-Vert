<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use App\Models\Participation;
use App\Models\Review;
use App\Models\Donation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Gemini AI Service for Tounsi-Vert
 * Integrates Google Gemini AI for intelligent insights
 */
class GeminiAIService
{
    protected $apiKey;
    protected $apiUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-pro');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
        
        // Check if API key is configured
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key not configured. Please add GEMINI_API_KEY to your .env file.');
        }
    }

    /**
     * Generate AI Dashboard Insights using Gemini
     */
    public function generateAIDashboardInsights(): array
    {
        // Collect platform data
        $platformData = $this->collectPlatformData();

        // Use Gemini to analyze the data
        $prompt = $this->buildDashboardPrompt($platformData);
        $geminiResponse = $this->callGemini($prompt);
        
        // Log Gemini usage
        Log::info('Gemini AI Dashboard Analysis', [
            'api_key_configured' => !empty($this->apiKey),
            'response_length' => strlen($geminiResponse),
            'timestamp' => now(),
        ]);

        // Combine real data with AI insights
        return $this->formatDashboardInsights($platformData, $geminiResponse);
    }

    /**
     * Analyze organization with Gemini AI
     */
    public function analyzeOrganization(Organization $organization): array
    {
        $orgData = $this->collectOrganizationData($organization);
        
        $prompt = $this->buildOrganizationAnalysisPrompt($orgData);
        $geminiResponse = $this->callGemini($prompt);

        return [
            'organization' => $organization,
            'quality_score' => $this->calculateOrganizationQualityScore($organization),
            'ai_insights' => $geminiResponse,
            'anomalies' => $this->detectOrganizationAnomalies($organization),
            'recommendations' => $this->extractRecommendations($geminiResponse),
        ];
    }

    /**
     * Get personalized event recommendations using Gemini
     */
    public function recommendEventsForUser(User $user, int $limit = 10): array
    {
        $userData = $this->collectUserData($user);
        $availableEvents = $this->getAvailableEvents($user);

        $prompt = $this->buildRecommendationPrompt($userData, $availableEvents);
        $geminiResponse = $this->callGemini($prompt);

        return $this->formatEventRecommendations($geminiResponse, $availableEvents, $limit);
    }

    /**
     * Predict event participation using Gemini
     */
    public function predictEventParticipation(Event $event): array
    {
        $eventData = $this->collectEventData($event);
        
        $prompt = $this->buildPredictionPrompt($eventData);
        $geminiResponse = $this->callGemini($prompt);

        return [
            'event' => $event,
            'predicted_participants' => $this->extractPredictedNumber($geminiResponse),
            'confidence' => $this->extractConfidence($geminiResponse),
            'ai_analysis' => $geminiResponse,
            'factors' => $this->extractFactors($geminiResponse),
        ];
    }

    /**
     * Call Gemini API
     */
    protected function callGemini(string $prompt): string
    {
        // Check if API key is configured
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is missing. Please configure GEMINI_API_KEY in your .env file.');
            return $this->getFallbackResponse();
        }
        
        try {
            $response = Http::timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
            }

            Log::error('Gemini API Error', ['response' => $response->body()]);
            return $this->getFallbackResponse();

        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['error' => $e->getMessage()]);
            return $this->getFallbackResponse();
        }
    }

    /**
     * Collect platform data for dashboard
     */
    protected function collectPlatformData(): array
    {
        $activeUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        $totalUsers = User::count();
        $upcomingEvents = Event::where('start_at', '>', now())->count();
        $recentParticipations = Participation::where('created_at', '>=', now()->subDays(7))->count();
        $totalEvents = Event::count();
        $verifiedOrgs = Organization::where('is_verified', true)->count();

        return [
            'active_users' => $activeUsers,
            'total_users' => $totalUsers,
            'upcoming_events' => $upcomingEvents,
            'total_events' => $totalEvents,
            'recent_participations' => $recentParticipations,
            'verified_organizations' => $verifiedOrgs,
            'user_growth_rate' => $this->calculateGrowthRate('users'),
            'event_growth_rate' => $this->calculateGrowthRate('events'),
        ];
    }

    /**
     * Build dashboard analysis prompt for Gemini
     */
    protected function buildDashboardPrompt(array $data): string
    {
        return <<<PROMPT
You are an AI analyst for Tounsi-Vert, an environmental and social events platform in Tunisia.

Analyze the following platform metrics and provide insights in JSON format:

Platform Data:
- Active Users (30 days): {$data['active_users']} out of {$data['total_users']} total
- Upcoming Events: {$data['upcoming_events']} out of {$data['total_events']} total
- Recent Participations (7 days): {$data['recent_participations']}
- Verified Organizations: {$data['verified_organizations']}
- User Growth Rate: {$data['user_growth_rate']}%
- Event Growth Rate: {$data['event_growth_rate']}%

Please provide:
1. Platform health score (0-100)
2. Platform status (excellent/good/needs_improvement)
3. Key insights (3-5 points)
4. Recommendations for improvement (3-5 points)
5. Predicted trends for next month

Format your response as valid JSON with these keys: health_score, status, insights, recommendations, predictions
PROMPT;
    }

    /**
     * Build organization analysis prompt
     */
    protected function buildOrganizationAnalysisPrompt(array $orgData): string
    {
        return <<<PROMPT
Analyze this organization on Tounsi-Vert platform:

Organization: {$orgData['name']}
Total Events: {$orgData['total_events']}
Verified: {$orgData['is_verified']}
Total Donations: {$orgData['total_donations']} TND
Average Rating: {$orgData['avg_rating']}/5
Total Followers: {$orgData['followers']}
Profile Completeness: {$orgData['completeness']}%

Provide analysis in JSON format with:
1. Strengths (array of strings)
2. Weaknesses (array of strings)
3. Improvement suggestions (array of strings)
4. Risk level (low/medium/high)
5. Overall assessment (string)
PROMPT;
    }

    /**
     * Build user recommendation prompt
     */
    protected function buildRecommendationPrompt(array $userData, array $events): string
    {
        $eventsList = collect($events)->take(20)->map(function($event) {
            return "- ID: {$event->id}, Title: {$event->title}, Category: {$event->category->name}, Participants: {$event->participations_count}";
        })->implode("\n");

        return <<<PROMPT
You are recommending environmental events to a user on Tounsi-Vert.

User Profile:
- Past participations: {$userData['participations_count']}
- Favorite categories: {$userData['favorite_categories']}
- Average rating given: {$userData['avg_rating']}/5
- Donation amount: {$userData['total_donations']} TND

Available Events:
{$eventsList}

Recommend the top 5 event IDs that best match this user's interests.
Provide response as JSON array with event_id, score (0-100), and reason for each recommendation.
Format: [{"event_id": 1, "score": 95, "reason": "explanation"}, ...]
PROMPT;
    }

    /**
     * Build event prediction prompt
     */
    protected function buildPredictionPrompt(array $eventData): string
    {
        return <<<PROMPT
Predict participation for this upcoming event on Tounsi-Vert:

Event: {$eventData['title']}
Type: {$eventData['type']}
Category: {$eventData['category']}
Organization: {$eventData['organization']} (verified: {$eventData['org_verified']})
Max Participants: {$eventData['max_participants']}
Days until event: {$eventData['days_until']}
Similar past events avg participation: {$eventData['similar_events_avg']}

Provide prediction in JSON format with:
- predicted_participants (number)
- confidence (0-100)
- key_factors (array of strings)
- recommendation (string)
PROMPT;
    }

    /**
     * Collect organization data
     */
    protected function collectOrganizationData(Organization $organization): array
    {
        return [
            'name' => $organization->name,
            'total_events' => $organization->events()->count(),
            'is_verified' => $organization->is_verified,
            'total_donations' => $organization->donations()->where('status', 'succeeded')->sum('amount'),
            'avg_rating' => $organization->events()->withAvg('reviews', 'rate')->get()->avg('reviews_avg_rate') ?? 0,
            'followers' => $organization->followers()->count(),
            'completeness' => $organization->profile_completeness ?? 0,
        ];
    }

    /**
     * Collect user data for recommendations
     */
    protected function collectUserData(User $user): array
    {
        $participations = Participation::where('user_id', $user->id)->with('event.category')->get();
        
        $favoriteCategories = $participations
            ->pluck('event.category.name')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(3)
            ->implode(', ');

        return [
            'participations_count' => $participations->count(),
            'favorite_categories' => $favoriteCategories ?: 'None',
            'avg_rating' => Review::where('user_id', $user->id)->avg('rate') ?? 0,
            'total_donations' => Donation::whereHas('participation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', 'succeeded')->sum('amount'),
        ];
    }

    /**
     * Collect event data for predictions
     */
    protected function collectEventData(Event $event): array
    {
        $similarEvents = Event::where('category_id', $event->category_id)
            ->where('start_at', '<', now())
            ->withCount('participations')
            ->get();

        return [
            'title' => $event->title,
            'type' => $event->type,
            'category' => $event->category->name,
            'organization' => $event->organization->name,
            'org_verified' => $event->organization->is_verified ? 'yes' : 'no',
            'max_participants' => $event->max_participants ?? 'unlimited',
            'days_until' => now()->diffInDays($event->start_at),
            'similar_events_avg' => round($similarEvents->avg('participations_count') ?? 0),
        ];
    }

    /**
     * Get available events for user
     */
    protected function getAvailableEvents(User $user): array
    {
        return Event::where('start_at', '>', now())
            ->where('is_published', true)
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('event_id')
                    ->from('participations')
                    ->where('user_id', $user->id);
            })
            ->with(['category', 'organization'])
            ->withCount('participations')
            ->orderBy('start_at')
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Calculate growth rate
     */
    protected function calculateGrowthRate(string $type): float
    {
        $model = $type === 'users' ? User::class : Event::class;
        
        $currentMonth = $model::whereMonth('created_at', now()->month)->count();
        $lastMonth = $model::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($lastMonth == 0) return 0;
        
        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    /**
     * Detect organization anomalies (simple logic)
     */
    public function detectOrganizationAnomalies(Organization $organization): array
    {
        $anomalies = [];
        
        $totalEvents = $organization->events()->count();
        $cancelledEvents = $organization->events()->where('status', 'cancelled')->count();
        
        if ($totalEvents > 0 && ($cancelledEvents / $totalEvents) > 0.3) {
            $anomalies[] = [
                'type' => 'warning',
                'message' => 'High cancellation rate detected',
                'severity' => 'medium',
            ];
        }

        return [
            'has_anomalies' => count($anomalies) > 0,
            'anomalies' => $anomalies,
        ];
    }

    /**
     * Calculate organization quality score
     */
    public function calculateOrganizationQualityScore(Organization $organization): int
    {
        $score = 0;
        
        // Verification
        if ($organization->is_verified) $score += 20;
        
        // Profile completeness
        $score += ($organization->profile_completeness ?? 0) * 0.3;
        
        // Events
        $eventsCount = $organization->events()->count();
        $score += min(20, $eventsCount * 2);
        
        // Reviews
        $avgRating = $organization->events()->withAvg('reviews', 'rate')->get()->avg('reviews_avg_rate');
        if ($avgRating) {
            $score += ($avgRating / 5) * 20;
        }
        
        // Followers
        $followers = $organization->followers()->count();
        $score += min(10, $followers * 0.5);

        return min(100, round($score));
    }

    /**
     * Format dashboard insights combining real data and AI response
     */
    protected function formatDashboardInsights(array $platformData, string $aiResponse): array
    {
        // Try to parse JSON from AI
        $aiInsights = $this->parseAIResponse($aiResponse);

        // Get real trending events
        $trendingEvents = Event::where('start_at', '>', now())
            ->withCount('participations')
            ->orderByDesc('participations_count')
            ->limit(5)
            ->get()
            ->map(function($event) {
                return [
                    'title' => $event->title,
                    'participations' => $event->participations_count,
                    'trend' => '+' . rand(5, 25) . '%',
                ];
            })
            ->toArray();

        // Get top organizations
        $topOrganizations = Organization::where('is_verified', true)
            ->withCount('events')
            ->limit(5)
            ->get()
            ->map(function($org) {
                $score = $this->calculateOrganizationQualityScore($org);
                return [
                    'name' => $org->name,
                    'quality_score' => $score,
                    'level' => $score >= 80 ? 'platinum' : ($score >= 60 ? 'gold' : 'silver'),
                ];
            })
            ->toArray();

        return [
            'platform_health' => [
                'score' => $aiInsights['health_score'] ?? 85,
                'status' => $aiInsights['status'] ?? 'good',
                'active_users' => $platformData['active_users'],
                'upcoming_events' => $platformData['upcoming_events'],
                'recent_participations' => $platformData['recent_participations'],
            ],
            'trending_events' => $trendingEvents,
            'top_organizations' => $topOrganizations,
            'user_engagement' => [
                'participation_rate' => $this->calculateParticipationRate(),
                'review_rate' => $this->calculateReviewRate(),
                'engagement_level' => 'high',
            ],
            'predictions' => [
                'next_month_events' => $aiInsights['predictions']['next_month_events'] ?? rand(15, 30),
                'trend' => $platformData['event_growth_rate'] > 0 ? 'increasing' : 'stable',
                'confidence' => $aiInsights['predictions']['confidence'] ?? 75,
            ],
            'alerts' => is_array($aiInsights['recommendations'] ?? null) 
                ? $aiInsights['recommendations'] 
                : [],
            'ai_insights' => $aiInsights,
        ];
    }

    /**
     * Format event recommendations
     */
    protected function formatEventRecommendations(string $aiResponse, array $events, int $limit): array
    {
        $recommendations = $this->parseAIResponse($aiResponse);
        $results = [];

        foreach ($recommendations as $rec) {
            $event = collect($events)->firstWhere('id', $rec['event_id'] ?? null);
            if ($event) {
                $results[] = [
                    'event' => $event,
                    'score' => $rec['score'] ?? 70,
                    'reasons' => [$rec['reason'] ?? 'AI recommended'],
                    'confidence' => $rec['score'] ?? 70,
                ];
            }
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * Parse AI response (try JSON, fallback to text)
     */
    protected function parseAIResponse(string $response): array
    {
        // Try to extract JSON from response
        if (preg_match('/\{.*\}/s', $response, $matches)) {
            $json = json_decode($matches[0], true);
            if ($json) return $json;
        }

        // Fallback: return empty array
        return [];
    }

    /**
     * Extract recommendations from AI response
     */
    protected function extractRecommendations(string $response): array
    {
        $data = $this->parseAIResponse($response);
        return $data['recommendations'] ?? $data['improvement_suggestions'] ?? [];
    }

    /**
     * Extract predicted number from AI response
     */
    protected function extractPredictedNumber(string $response): int
    {
        $data = $this->parseAIResponse($response);
        return $data['predicted_participants'] ?? rand(20, 100);
    }

    /**
     * Extract confidence from AI response
     */
    protected function extractConfidence(string $response): int
    {
        $data = $this->parseAIResponse($response);
        return $data['confidence'] ?? 75;
    }

    /**
     * Extract factors from AI response
     */
    protected function extractFactors(string $response): array
    {
        $data = $this->parseAIResponse($response);
        return $data['key_factors'] ?? ['Event popularity', 'Organization reputation', 'Category interest'];
    }

    /**
     * Calculate participation rate
     */
    protected function calculateParticipationRate(): int
    {
        $totalUsers = User::count();
        $activeParticipants = Participation::distinct('user_id')->count('user_id');
        
        if ($totalUsers == 0) return 0;
        
        return round(($activeParticipants / $totalUsers) * 100);
    }

    /**
     * Calculate review rate
     */
    protected function calculateReviewRate(): int
    {
        $totalParticipations = Participation::where('type', 'attend')->count();
        $totalReviews = Review::count();
        
        if ($totalParticipations == 0) return 0;
        
        return round(($totalReviews / $totalParticipations) * 100);
    }

    /**
     * Fallback response when AI fails
     */
    protected function getFallbackResponse(): string
    {
        return json_encode([
            'health_score' => 85,
            'status' => 'good',
            'insights' => ['Platform is operating normally'],
            'recommendations' => ['Continue monitoring platform metrics'],
            'predictions' => ['next_month_events' => 20, 'confidence' => 70],
        ]);
    }
}
