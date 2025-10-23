<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OrganizerAiController extends Controller
{
    /**
     * Display the AI dashboard with insights and recommendations
     */
    public function index()
    {
        // Get organizer's organization
        $organization = Organization::where('owner_id', auth()->id())->first();
        
        if (!$organization) {
            return redirect()->route('organizer.dashboard')
                ->with('error', 'You must have an organization to access AI insights.');
        }

        // Get donation data for last 90 days
        $startDate = Carbon::now()->subDays(90);
        $donations = Donation::where('organization_id', $organization->id)
            ->where('created_at', '>=', $startDate)
            ->where('status', 'succeeded')
            ->get();

        // Calculate aggregated statistics
        $totalAmount = $donations->sum('amount');
        $donationCount = $donations->count();
        $averageDonation = $donationCount > 0 ? $totalAmount / $donationCount : 0;
        
        // Monthly breakdown
        $monthlyData = $donations->groupBy(function($donation) {
            return $donation->created_at->format('Y-m');
        })->map(function($month) {
            return [
                'count' => $month->count(),
                'amount' => $month->sum('amount')
            ];
        });

        // Generate AI insights
        $aiInsights = $this->generateDonationInsights($totalAmount, $donationCount, $averageDonation, $monthlyData, $organization->name);
        $nextBestActions = $this->generateNextBestActions($totalAmount, $donationCount, $monthlyData, $organization->name);
        $thankYouTemplate = $this->generateThankYouTemplate($organization->name);

        return view('organizer.dashboard.ai', compact(
            'organization',
            'totalAmount',
            'donationCount',
            'averageDonation',
            'aiInsights',
            'nextBestActions',
            'thankYouTemplate'
        ));
    }

    /**
     * Generate AI-powered donation insights using OpenAI or Gemini
     */
    private function generateDonationInsights($totalAmount, $donationCount, $averageDonation, $monthlyData, $orgName)
    {
        try {
            // Prepare context for AI
            $context = "Organisation: {$orgName}\n";
            $context .= "Période: 90 derniers jours\n";
            $context .= "Montant total: " . number_format($totalAmount, 2) . " TND\n";
            $context .= "Nombre de dons: {$donationCount}\n";
            $context .= "Don moyen: " . number_format($averageDonation, 2) . " TND\n";
            $context .= "Tendance mensuelle: " . $monthlyData->count() . " mois actifs\n";

            $prompt = "En tant qu'expert en analyse de dons pour une organisation écologique, résumez les tendances de dons suivantes en un paragraphe court (3-4 phrases) en français:\n\n{$context}\n\nFocus sur: montant, fréquence, et motivation probable des donateurs.";

            // Option 1: OpenAI API
            if (config('services.openai.api_key')) {
                $response = Http::withToken(config('services.openai.api_key'))
                    ->timeout(15)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Tu es un analyste de dons pour organisations écologiques.'],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'max_tokens' => 200,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    return $response->json('choices.0.message.content');
                }
            }

            // Option 2: Gemini API (fallback or primary)
            if (config('services.gemini.api_key')) {
                $response = Http::timeout(15)
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent', [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ]
                    ], [
                        'key' => config('services.gemini.api_key')
                    ]);

                if ($response->successful()) {
                    return $response->json('candidates.0.content.parts.0.text');
                }
            }

            // Fallback: Generate simple insights without AI
            return $this->generateFallbackInsights($totalAmount, $donationCount, $averageDonation);

        } catch (\Exception $e) {
            \Log::error('AI Insights Error: ' . $e->getMessage());
            return "Analyse IA indisponible pour le moment. Veuillez réessayer plus tard.";
        }
    }

    /**
     * Generate next-best actions using AI
     */
    private function generateNextBestActions($totalAmount, $donationCount, $monthlyData, $orgName)
    {
        try {
            $context = "Organisation: {$orgName}\n";
            $context .= "Total collecté (90j): " . number_format($totalAmount, 2) . " TND\n";
            $context .= "Nombre de dons: {$donationCount}\n";
            
            // Add variation to prompts to get different results each time
            $variations = [
                "Génère 3 recommandations innovantes et uniques (une ligne chacune) en français pour booster les campagnes de dons de cette organisation écologique:\n\n{$context}\n\nSois créatif et propose des idées différentes à chaque fois.",
                "Propose 3 actions stratégiques différentes (une ligne chacune) en français pour optimiser les collectes de dons:\n\n{$context}\n\nFocus: approches modernes et efficaces.",
                "Liste 3 tactiques concrètes et variées (une ligne chacune) en français pour améliorer l'engagement des donateurs:\n\n{$context}\n\nPense à des angles différents."
            ];
            
            $prompt = $variations[array_rand($variations)] . "\n[Timestamp: " . time() . "]";

            // Try OpenAI first
            if (config('services.openai.api_key')) {
                $response = Http::withToken(config('services.openai.api_key'))
                    ->timeout(15)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Tu es un consultant créatif en stratégie de dons. Propose toujours des idées nouvelles et variées.'],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'max_tokens' => 150,
                        'temperature' => 1.0,
                        'presence_penalty' => 0.6,
                        'frequency_penalty' => 0.6,
                    ]);

                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content');
                    return $this->parseActions($content);
                }
            }

            // Fallback actions
            return $this->generateFallbackActions($donationCount);

        } catch (\Exception $e) {
            \Log::error('AI Actions Error: ' . $e->getMessage());
            return $this->generateFallbackActions($donationCount);
        }
    }

    /**
     * Generate thank-you template using AI
     */
    private function generateThankYouTemplate($orgName)
    {
        try {
            // Add variation to get different thank-you messages each time
            $styles = [
                "formel et professionnel",
                "chaleureux et familier",
                "poétique et inspirant",
                "direct et sincère",
                "enthousiaste et motivant"
            ];
            
            $style = $styles[array_rand($styles)];
            
            $prompt = "Écris un message de remerciement unique et {$style} (4-5 lignes) en français pour les donateurs de {$orgName}, une organisation écologique. Sois créatif et original. Le message doit être prêt à envoyer.\n[Style: {$style}] [Timestamp: " . time() . "]";

            // Try OpenAI
            if (config('services.openai.api_key')) {
                $response = Http::withToken(config('services.openai.api_key'))
                    ->timeout(15)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Tu es un expert créatif en communication pour organisations écologiques. Génère toujours des messages uniques et variés.'],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'max_tokens' => 200,
                        'temperature' => 1.0,
                        'presence_penalty' => 0.7,
                        'frequency_penalty' => 0.7,
                    ]);

                if ($response->successful()) {
                    return $response->json('choices.0.message.content');
                }
            }

            // Fallback template
            return $this->generateFallbackThankYou($orgName);

        } catch (\Exception $e) {
            \Log::error('AI Thank You Error: ' . $e->getMessage());
            return $this->generateFallbackThankYou($orgName);
        }
    }

    /**
     * Fallback insights when AI is unavailable
     */
    private function generateFallbackInsights($totalAmount, $donationCount, $averageDonation)
    {
        if ($donationCount === 0) {
            return "Aucun don reçu au cours des 90 derniers jours. C'est le moment de lancer une campagne pour mobiliser votre communauté autour de votre mission écologique.";
        }

        $text = "Au cours des 90 derniers jours, votre organisation a collecté " . number_format($totalAmount, 2) . " TND grâce à {$donationCount} don(s). ";
        $text .= "Le don moyen s'élève à " . number_format($averageDonation, 2) . " TND, reflétant l'engagement de vos donateurs. ";
        $text .= "Cette tendance montre un soutien régulier à votre mission environnementale.";
        
        return $text;
    }

    /**
     * Fallback actions when AI is unavailable
     */
    private function generateFallbackActions($donationCount)
    {
        $allActions = [];
        
        if ($donationCount === 0) {
            $allActions = [
                ["Lancez une campagne de sensibilisation sur les réseaux sociaux", "Organisez un événement écologique pour attirer de nouveaux donateurs", "Créez une newsletter mensuelle pour partager vos impacts"],
                ["Développez une stratégie de communication digitale ciblée", "Proposez des partenariats avec des entreprises locales", "Créez du contenu vidéo montrant l'impact de votre mission"],
                ["Organisez des webinaires sur les enjeux écologiques", "Lancez un programme d'ambassadeurs bénévoles", "Créez une page de don optimisée sur votre site web"]
            ];
        } else {
            $allActions = [
                ["Remerciez personnellement vos donateurs réguliers pour renforcer la fidélité", "Partagez l'impact concret de leurs dons via des photos et témoignages", "Proposez des options de dons récurrents pour stabiliser vos revenus"],
                ["Organisez un événement de remerciement pour vos donateurs", "Envoyez des rapports d'impact trimestriels détaillés", "Créez un programme de parrainage entre donateurs"],
                ["Lancez une campagne de fin d'année avec objectif précis", "Segmentez vos donateurs pour une communication personnalisée", "Proposez des niveaux de dons avec reconnaissance publique"]
            ];
        }
        
        return $allActions[array_rand($allActions)];
    }

    /**
     * Fallback thank-you template
     */
    private function generateFallbackThankYou($orgName)
    {
        $templates = [
            "Chère donatrice, cher donateur,\n\nAu nom de toute l'équipe de {$orgName}, nous vous remercions sincèrement pour votre généreuse contribution. Votre soutien est essentiel pour poursuivre nos actions en faveur de l'environnement.\n\nGrâce à vous, nous pouvons continuer à protéger notre planète et créer un avenir plus vert.\n\nAvec toute notre gratitude,\nL'équipe {$orgName}",
            
            "Bonjour,\n\nToute l'équipe de {$orgName} vous exprime sa profonde reconnaissance pour votre don généreux. Votre geste fait une réelle différence dans notre combat pour la protection de l'environnement.\n\nEnsemble, nous bâtissons un futur plus durable. Merci d'être à nos côtés dans cette mission !\n\nCordialement,\n{$orgName}",
            
            "Cher.ère ami.e de la nature,\n\nVotre contribution à {$orgName} nous touche profondément. Chaque don, comme le vôtre, nous permet d'avancer concrètement dans nos projets écologiques.\n\nVous êtes un maillon essentiel de notre action collective. Merci infiniment pour votre confiance et votre engagement !\n\nÀ bientôt,\nL'équipe {$orgName}",
            
            "Un grand merci !\n\nVotre soutien à {$orgName} illumine notre journée et renforce notre détermination. Grâce à votre générosité, nous pouvons poursuivre nos actions concrètes pour l'environnement.\n\nVotre engagement fait la différence. Nous sommes fiers de vous compter parmi nos soutiens !\n\nChaleureusement,\n{$orgName}",
            
            "Bonjour cher donateur, chère donatrice,\n\nC'est avec une immense gratitude que {$orgName} reçoit votre don. Votre acte de générosité nous permet de continuer à œuvrer pour un monde plus respectueux de la nature.\n\nVous faites partie de notre famille écologique. Merci du fond du cœur !\n\nBien à vous,\nL'équipe {$orgName}"
        ];
        
        return $templates[array_rand($templates)];
    }

    /**
     * Parse AI response into actionable items
     */
    private function parseActions($content)
    {
        // Extract bullet points or numbered items
        $lines = explode("\n", $content);
        $actions = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            // Remove bullets, numbers, dashes
            $line = preg_replace('/^[\d\.\-\*•]\s*/', '', $line);
            if (!empty($line) && strlen($line) > 10) {
                $actions[] = $line;
            }
        }
        
        // Return first 3 actions, or generate fallback
        return array_slice($actions, 0, 3) ?: $this->generateFallbackActions(1);
    }
}
