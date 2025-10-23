<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\TounsiVertAIService;
use Illuminate\Http\Request;

class AIRecommendationController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->aiService = new TounsiVertAIService();
    }

    /**
     * Page de recommandations personnalisées
     */
    public function index()
    {
        $user = auth()->user();
        $recommendations = $this->aiService->recommendEventsForUser($user, 10);
        
        return view('member.ai-recommendations', compact('recommendations'));
    }

    /**
     * API pour obtenir des recommandations
     */
    public function getRecommendations(Request $request)
    {
        $user = auth()->user();
        $limit = $request->get('limit', 5);
        
        $recommendations = $this->aiService->recommendEventsForUser($user, $limit);
        
        return response()->json($recommendations);
    }
}
