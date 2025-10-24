<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Generate blog title and content from an image using GPT-4 Vision
     */
    public function generateFromImage(string $imagePath): array
    {
        try {
            $imageUrl = Storage::url($imagePath);
            $fullImageUrl = url($imageUrl);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an environmental content creator for TounsiVert, a Tunisian environmental platform. Generate engaging blog content about environmental topics, sustainability, and eco-friendly practices in Tunisia.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyze this image and create a blog post about it. Return a JSON with "title" and "content" fields. The title should be catchy and under 100 characters. The content should be 200-500 words, engaging, and related to environmental topics in Tunisia. Write in a friendly, informative tone.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => $fullImageUrl
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                
                // Try to parse JSON from the response
                $jsonContent = $this->extractJson($content);
                
                if ($jsonContent) {
                    return [
                        'title' => $jsonContent['title'] ?? 'Environmental Awareness',
                        'content' => $jsonContent['content'] ?? $content,
                    ];
                }
                
                // Fallback: split content into title and body
                return $this->splitContent($content);
            }

            return [
                'title' => '',
                'content' => '',
                'error' => 'Failed to generate content from image'
            ];

        } catch (\Exception $e) {
            return [
                'title' => '',
                'content' => '',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Enhance existing title and content with AI
     */
    public function enhanceContent(string $title = '', string $content = ''): array
    {
        try {
            $prompt = "Improve this blog post for TounsiVert, a Tunisian environmental platform:\n\n";
            
            if ($title) {
                $prompt .= "Title: $title\n";
            }
            
            if ($content) {
                $prompt .= "Content: $content\n";
            }
            
            $prompt .= "\nMake it more engaging, fix grammar, and ensure it's environmentally focused. Return a JSON with 'title' and 'content' fields.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert content editor for environmental blogs in Tunisia. Improve writing while maintaining the original message.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                $jsonContent = $this->extractJson($content);
                
                if ($jsonContent) {
                    return [
                        'title' => $jsonContent['title'] ?? $title,
                        'content' => $jsonContent['content'] ?? $content,
                    ];
                }
                
                return $this->splitContent($content);
            }

            return [
                'title' => $title,
                'content' => $content,
                'error' => 'Failed to enhance content'
            ];

        } catch (\Exception $e) {
            return [
                'title' => $title,
                'content' => $content,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate a banner image from title and content using DALL-E
     */
    public function generateBannerImage(string $title, string $content = ''): ?string
    {
        try {
            $prompt = "Create a vibrant, professional banner image for a blog post titled: '$title'. ";
            $prompt .= "The image should be related to environmental topics, nature, sustainability, and green living in Tunisia. ";
            $prompt .= "Style: Modern, clean, eco-friendly, with green and natural colors. No text in the image.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post($this->baseUrl . '/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1792x1024',
                'quality' => 'standard',
            ]);

            if ($response->successful()) {
                $imageUrl = $response->json()['data'][0]['url'] ?? null;
                
                if ($imageUrl) {
                    // Download and save the image
                    $imageContent = file_get_contents($imageUrl);
                    $filename = 'blogs/ai-generated/' . uniqid() . '.png';
                    Storage::disk('public')->put($filename, $imageContent);
                    
                    return $filename;
                }
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('OpenAI Image Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract JSON from OpenAI response
     */
    private function extractJson(string $content): ?array
    {
        // Try to find JSON in the response
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            try {
                $json = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $json;
                }
            } catch (\Exception $e) {
                // Continue to fallback
            }
        }
        
        return null;
    }

    /**
     * Split content into title and body
     */
    private function splitContent(string $content): array
    {
        $lines = explode("\n", trim($content));
        $title = $lines[0] ?? 'Environmental Awareness';
        $body = implode("\n", array_slice($lines, 1));
        
        // Clean title
        $title = preg_replace('/^(Title:|#)\s*/i', '', $title);
        $title = trim($title, ' ":');
        
        return [
            'title' => substr($title, 0, 200),
            'content' => trim($body)
        ];
    }
}
