<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TounsiVertAIService;
use App\Models\Organization;
use App\Models\Event;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        $this->aiService = new TounsiVertAIService();
    }

    /**
     * Dashboard IA Principal
     */
    public function dashboard()
    {
        $insights = $this->aiService->generateAIDashboardInsights();
        
        return view('admin.ai.dashboard', compact('insights'));
    }

    /**
     * Analyse d'une organisation
     */
    public function analyzeOrganization(Organization $organization)
    {
        $anomalies = $this->aiService->detectOrganizationAnomalies($organization);
        $qualityScore = $this->aiService->calculateOrganizationQualityScore($organization);
        
        return view('admin.ai.organization-analysis', compact('organization', 'anomalies', 'qualityScore'));
    }

    /**
     * Prédiction pour un événement
     */
    public function predictEvent(Event $event)
    {
        $prediction = $this->aiService->predictEventParticipation($event);
        
        return response()->json($prediction);
    }

    /**
     * Liste des organisations avec anomalies
     */
    public function organizationsWithAnomalies()
    {
        $organizations = Organization::with('events')->get();
        $results = [];

        foreach ($organizations as $org) {
            $analysis = $this->aiService->detectOrganizationAnomalies($org);
            if ($analysis['has_anomalies']) {
                $results[] = [
                    'organization' => $org,
                    'analysis' => $analysis,
                ];
            }
        }

        return view('admin.ai.anomalies', compact('results'));
    }
}
