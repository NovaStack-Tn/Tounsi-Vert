<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogAIController extends Controller
{
    protected $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->middleware('auth');
        $this->openAI = $openAI;
    }

    /**
     * Generate blog content from uploaded image
     */
    public function generateFromImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            // Store the image temporarily
            $imagePath = $request->file('image')->store('temp/ai-analysis', 'public');
            
            // Generate content using AI
            $result = $this->openAI->generateFromImage($imagePath);
            
            // Keep the image path for the user to use
            $result['image_path'] = $imagePath;
            
            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'title' => $result['title'],
                'content' => $result['content'],
                'image_path' => $imagePath,
                'message' => 'Content generated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enhance existing blog content
     */
    public function enhanceContent(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        try {
            $result = $this->openAI->enhanceContent(
                $request->input('title', ''),
                $request->input('content', '')
            );

            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'title' => $result['title'],
                'content' => $result['content'],
                'message' => 'Content enhanced successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate banner image from title and content
     */
    public function generateBannerImage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        try {
            $imagePath = $this->openAI->generateBannerImage(
                $request->input('title'),
                $request->input('content', '')
            );

            if (!$imagePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate banner image'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'image_path' => $imagePath,
                'image_url' => Storage::url($imagePath),
                'message' => 'Banner image generated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate image: ' . $e->getMessage()
            ], 500);
        }
    }
}
