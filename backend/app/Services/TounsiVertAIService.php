<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use App\Models\Participation;
use App\Models\Review;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Tounsi-Vert AI Service - SIMPLIFIÉ
 * 
 * 3 Fonctionnalités Principales:
 * 1. Recommandation d'événements (basé sur historique)
 * 2. Détection d'anomalies (organisations suspectes)
 * 3. Statistiques intelligentes (dashboard)
 */
class TounsiVertAIService
{
    /**
     * 1. RECOMMANDATION SIMPLE
     * Trouve les événements similaires à ceux que l'utilisateur aime
     */
    public function recommendEventsForUser(User $user, int $limit = 10): array
    {
        // Trouver les catégories favorites de l'utilisateur
        $favoriteCategories = Participation::where('user_id', $user->id)
            ->join('events', 'participations.event_id', '=', 'events.id')
            ->select('events.category_id')
            ->groupBy('events.category_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(2)
            ->pluck('category_id')
            ->toArray();
        
        // Si pas d'historique, recommander les événements populaires
        if (empty($favoriteCategories)) {
            return $this->getPopularEvents($limit);
        }
        
        // Trouver les événements à venir dans ces catégories
        $recommendations = Event::where('start_at', '>', now())
            ->where('status', 'approved')
            ->whereIn('category_id', $favoriteCategories)
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('event_id')
                    ->from('participations')
                    ->where('user_id', $user->id);
            })
            ->with(['category', 'organization'])
            ->withCount('participations')
            ->orderByDesc('participations_count')
            ->limit($limit)
            ->get();
        
        // Formater les résultats
        return $recommendations->map(function($event) {
            return [
                'event' => $event,
                'score' => min(100, $event->participations_count * 2),
                'reasons' => ['Catégorie que vous aimez', 'Populaire'],
                'confidence' => 80,
            ];
        })->toArray();
    }
    
    /**
     * Événements populaires (si pas d'historique)
     */
    private function getPopularEvents(int $limit): array
    {
        $events = Event::where('start_at', '>', now())
            ->where('status', 'approved')
            ->with(['category', 'organization'])
            ->withCount('participations')
            ->orderByDesc('participations_count')
            ->limit($limit)
            ->get();
        
        return $events->map(function($event) {
            return [
                'event' => $event,
                'score' => 70,
                'reasons' => ['Très populaire'],
                'confidence' => 70,
            ];
        })->toArray();
    }
    
    /**
     * 2. DÉTECTION SIMPLE D'ANOMALIES
     * Vérifie 2 choses: trop d'événements et trop d'annulations
     */
    public function detectOrganizationAnomalies(Organization $organization): array
    {
        $anomalies = [];
        $riskScore = 0;
        
        // Vérification 1: Trop d'événements récents?
        $recentEvents = $organization->events()
            ->where('created_at', '>', now()->subDays(7))
            ->count();
        
        if ($recentEvents > 10) {
            $anomalies[] = [
                'type' => 'excessive_events',
                'severity' => 'high',
                'message' => "{$recentEvents} événements créés en 7 jours (suspect)",
            ];
            $riskScore += 50;
        }
        
        // Vérification 2: Trop d'annulations?
        $totalEvents = $organization->events()->count();
        $cancelledEvents = $organization->events()->where('status', 'cancelled')->count();
        
        if ($totalEvents > 5) {
            $cancellationRate = ($cancelledEvents / $totalEvents) * 100;
            if ($cancellationRate > 30) {
                $anomalies[] = [
                    'type' => 'high_cancellation',
                    'severity' => 'medium',
                    'message' => "{$cancellationRate}% d'annulations (trop élevé)",
                ];
                $riskScore += 30;
            }
        }
        
        // Résultat simple
        $hasProblems = count($anomalies) > 0;
        $level = $riskScore >= 50 ? 'high' : ($riskScore >= 30 ? 'medium' : 'low');
        
        return [
            'has_anomalies' => $hasProblems,
            'anomaly_count' => count($anomalies),
            'anomalies' => $anomalies,
            'risk_score' => $riskScore,
            'risk_level' => $level,
            'recommendation' => $hasProblems ? 'Vérifier cette organisation' : 'Rien à signaler',
        ];
    }
    
    /**
     * 3. PRÉDICTION SIMPLE
     * Estime le nombre de participants basé sur l'historique
     */
    public function predictEventParticipation(Event $event): array
    {
        // Calculer la moyenne des événements passés de cette organisation
        $avgParticipation = Event::where('organization_id', $event->organization_id)
            ->where('start_at', '<', now())
            ->withCount('participations')
            ->get()
            ->avg('participations_count');
        
        // Si pas d'historique, utiliser une valeur par défaut
        $prediction = $avgParticipation > 0 ? round($avgParticipation) : 20;
        
        return [
            'predicted_participants' => (int) $prediction,
            'confidence' => $avgParticipation > 0 ? 75 : 50,
            'factors' => ['Basé sur l\'historique de l\'organisation'],
            'range' => [
                'min' => max(5, (int) ($prediction * 0.7)),
                'max' => (int) ($prediction * 1.5),
            ],
        ];
    }
    
    /**
     * 4. ANALYSE SENTIMENT (SIMPLE)
     * Compte les mots positifs vs négatifs
     */
    public function analyzeSentiment(string $text): array
    {
        $text = strtolower($text);
        $positiveWords = ['excellent', 'super', 'bien', 'bon', 'génial'];
        $negativeWords = ['mauvais', 'nul', 'horrible', 'mal'];
        
        $positive = 0;
        $negative = 0;
        
        foreach ($positiveWords as $word) {
            $positive += substr_count($text, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negative += substr_count($text, $word);
        }
        
        if ($positive > $negative) {
            return ['sentiment' => 'positive', 'emoji' => '😊'];
        } elseif ($negative > $positive) {
            return ['sentiment' => 'negative', 'emoji' => '😞'];
        }
        
        return ['sentiment' => 'neutral', 'emoji' => '😐'];
    }
    
    /**
     * 5. SCORE QUALITÉ SIMPLE
     * Basé sur le nombre d'événements uniquement
     */
    public function calculateOrganizationQualityScore(Organization $organization): array
    {
        // Score simple: nombre d'événements
        $eventCount = $organization->events()->count();
        $totalScore = min(100, $eventCount * 10); // 10 points par événement
        
        // Niveau
        if ($totalScore >= 80) {
            $level = 'platinum';
            $badge = '🏆';
        } elseif ($totalScore >= 60) {
            $level = 'gold';
            $badge = '🥇';
        } elseif ($totalScore >= 40) {
            $level = 'silver';
            $badge = '🥈';
        } else {
            $level = 'bronze';
            $badge = '🥉';
        }
        
        return [
            'total_score' => $totalScore,
            'max_score' => 100,
            'level' => $level,
            'badge' => $badge,
            'scores_breakdown' => ['events' => $eventCount],
        ];
    }
    
    /**
     * 6. DASHBOARD SIMPLE
     * Statistiques de base pour les admins
     */
    public function generateAIDashboardInsights(): array
    {
        return [
            'platform_health' => $this->analyzePlatformHealth(),
            'trending_events' => $this->getTrendingEvents(),
            'top_organizations' => $this->getTopOrganizations(),
            'user_engagement' => $this->analyzeUserEngagement(),
            'predictions' => $this->generatePredictions(),
            'alerts' => $this->generateAlerts(),
        ];
    }
    
    /**
     * Santé de la plateforme (simplifié)
     */
    private function analyzePlatformHealth(): array
    {
        $activeUsers = User::where('created_at', '>', now()->subDays(30))->count();
        $upcomingEvents = Event::where('start_at', '>', now())->count();
        $recentParticipations = Participation::where('created_at', '>', now()->subDays(7))->count();
        
        // Score simple basé sur l'activité
        $score = min(100, ($activeUsers * 2) + ($upcomingEvents * 3) + ($recentParticipations * 2));
        
        return [
            'score' => $score,
            'status' => $score >= 70 ? 'excellent' : ($score >= 50 ? 'good' : 'needs_attention'),
            'active_users' => $activeUsers,
            'upcoming_events' => $upcomingEvents,
            'recent_participations' => $recentParticipations,
        ];
    }
    
    /**
     * Obtenir les événements tendance
     */
    private function getTrendingEvents(): array
    {
        return Event::where('start_at', '>', now())
            ->withCount('participations')
            ->orderByDesc('participations_count')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'participations' => $e->participations_count,
                'trend' => 'rising',
            ])
            ->toArray();
    }
    
    /**
     * Obtenir les meilleures organisations
     */
    private function getTopOrganizations(): array
    {
        return Organization::withCount('events')
            ->orderByDesc('events_count')
            ->limit(5)
            ->get()
            ->map(function($org) {
                $quality = $this->calculateOrganizationQualityScore($org);
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'quality_score' => $quality['total_score'],
                    'level' => $quality['level'],
                ];
            })
            ->toArray();
    }
    
    /**
     * Analyser l'engagement utilisateur
     */
    private function analyzeUserEngagement(): array
    {
        $totalUsers = User::count();
        $participatingUsers = Participation::distinct('user_id')->count('user_id');
        $reviewingUsers = Review::distinct('user_id')->count('user_id');
        
        return [
            'participation_rate' => $totalUsers > 0 ? round(($participatingUsers / $totalUsers) * 100, 2) : 0,
            'review_rate' => $totalUsers > 0 ? round(($reviewingUsers / $totalUsers) * 100, 2) : 0,
            'engagement_level' => $participatingUsers > ($totalUsers * 0.3) ? 'high' : 'moderate',
        ];
    }
    
    /**
     * Générer des prédictions
     */
    private function generatePredictions(): array
    {
        $lastMonthEvents = Event::where('created_at', '>=', now()->subMonth())->count();
        $previousMonthEvents = Event::where('created_at', '>=', now()->subMonths(2))
            ->where('created_at', '<', now()->subMonth())->count();
        
        $trend = $previousMonthEvents > 0 
            ? (($lastMonthEvents - $previousMonthEvents) / $previousMonthEvents) * 100 
            : 0;
        
        return [
            'next_month_events' => round($lastMonthEvents * (1 + ($trend / 100))),
            'trend' => $trend > 0 ? 'increasing' : 'decreasing',
            'confidence' => 75,
        ];
    }
    
    /**
     * Générer des alertes
     */
    private function generateAlerts(): array
    {
        $alerts = [];
        
        // Alerte: Événements sans participants
        $eventsWithoutParticipants = Event::where('start_at', '>', now())
            ->whereDoesntHave('participations')
            ->count();
        
        if ($eventsWithoutParticipants > 5) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$eventsWithoutParticipants} événements sans participants",
                'action' => 'Promouvoir ces événements',
            ];
        }
        
        return $alerts;
    }
    
}
