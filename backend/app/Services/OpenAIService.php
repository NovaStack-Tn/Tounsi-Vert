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
            // Check if API key is set
            if (!$this->apiKey) {
                \Log::error('OpenAI API key not configured');
                return [
                    'title' => '',
                    'content' => '',
                    'error' => 'OpenAI API key not configured. Please add OPENAI_API_KEY to your .env file.'
                ];
            }

            // Get the full path to the image
            $fullPath = Storage::disk('public')->path($imagePath);
            
            // Check if file exists
            if (!file_exists($fullPath)) {
                \Log::error('Image file not found: ' . $fullPath);
                return [
                    'title' => '',
                    'content' => '',
                    'error' => 'Image file not found'
                ];
            }

            // Read image and convert to base64
            $imageData = file_get_contents($fullPath);
            $base64Image = base64_encode($imageData);
            
            // Detect mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fullPath);
            finfo_close($finfo);

            \Log::info('Sending image to OpenAI Vision API', [
                'mime_type' => $mimeType,
                'image_size' => strlen($imageData)
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post($this->baseUrl . '/chat/completions', [
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
                                'text' => 'Analyze this image and create a blog post about it. Return ONLY a JSON object with "title" and "content" fields. The title should be catchy and under 100 characters. The content should be 200-500 words, engaging, and related to environmental topics. Write in a friendly, informative tone. Return ONLY the JSON, no other text.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}"
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            \Log::info('OpenAI Response Status: ' . $response->status());

            if ($response->successful()) {
                $responseData = $response->json();
                $content = $responseData['choices'][0]['message']['content'] ?? '';
                
                \Log::info('OpenAI Response Content', ['content' => substr($content, 0, 200)]);
                
                // Try to parse JSON from the response
                $jsonContent = $this->extractJson($content);
                
                if ($jsonContent && isset($jsonContent['title']) && isset($jsonContent['content'])) {
                    return [
                        'title' => $jsonContent['title'],
                        'content' => $jsonContent['content'],
                    ];
                }
                
                // Fallback: split content into title and body
                $splitContent = $this->splitContent($content);
                
                if (!empty($splitContent['title']) && !empty($splitContent['content'])) {
                    return $splitContent;
                }
                
                // If still no content, return error
                return [
                    'title' => '',
                    'content' => '',
                    'error' => 'Could not parse AI response. Raw content: ' . substr($content, 0, 100)
                ];
            }

            $errorBody = $response->body();
            \Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'body' => $errorBody
            ]);

            return [
                'title' => '',
                'content' => '',
                'error' => 'OpenAI API Error: ' . $response->status() . ' - ' . substr($errorBody, 0, 200)
            ];

        } catch (\Exception $e) {
            \Log::error('Exception in generateFromImage', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'title' => '',
                'content' => '',
                'error' => 'Exception: ' . $e->getMessage()
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
        // Remove markdown code blocks if present
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        $content = trim($content);
        
        // Try direct JSON decode first
        try {
            $json = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return $json;
            }
        } catch (\Exception $e) {
            // Continue to regex approach
        }
        
        // Try to find JSON in the response using regex
        if (preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $content, $matches)) {
            try {
                $json = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                    \Log::info('Extracted JSON from response', ['json' => $json]);
                    return $json;
                }
            } catch (\Exception $e) {
                \Log::error('Failed to decode extracted JSON', ['error' => $e->getMessage()]);
            }
        }
        
        \Log::warning('Could not extract JSON from content', ['content' => substr($content, 0, 200)]);
        return null;
    }

    /**
     * Split content into title and body
     */
    private function splitContent(string $content): array
    {
        $lines = array_filter(explode("\n", trim($content)), fn($line) => !empty(trim($line)));
        $lines = array_values($lines); // Re-index array
        
        $title = $lines[0] ?? 'Environmental Awareness';
        $body = implode("\n", array_slice($lines, 1));
        
        // Clean title - remove common prefixes
        $title = preg_replace('/^(Title:|#|##|###|\*\*Title\*\*:?)\s*/i', '', $title);
        $title = trim($title, ' ":\*');
        
        // If title is still empty, use default
        if (empty($title)) {
            $title = 'Environmental Awareness';
        }
        
        // If body is empty but we have content, use all content as body
        if (empty($body) && !empty($content)) {
            $body = $content;
        }
        
        \Log::info('Split content into title and body', [
            'title' => $title,
            'content_length' => strlen($body)
        ]);
        
        return [
            'title' => substr($title, 0, 200),
            'content' => trim($body)
        ];
    }
}
