<?php

/**
 * Quick OpenAI API Test Script
 * Run this to verify your OpenAI setup
 * 
 * Usage: php test-openai.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\OpenAIService;
use Illuminate\Support\Facades\Storage;

echo "=================================\n";
echo "OpenAI Configuration Test\n";
echo "=================================\n\n";

// 1. Check if API key is set
$apiKey = config('services.openai.api_key');
if (!$apiKey || $apiKey === 'your-openai-api-key-here') {
    echo "❌ FAIL: OpenAI API key not configured!\n";
    echo "   Please set OPENAI_API_KEY in your .env file\n";
    echo "   Get your key from: https://platform.openai.com/api-keys\n\n";
    exit(1);
} else {
    echo "✅ PASS: OpenAI API key is configured\n";
    echo "   Key: " . substr($apiKey, 0, 7) . "..." . substr($apiKey, -4) . "\n\n";
}

// 2. Check if storage is configured
$storagePath = Storage::disk('public')->path('');
if (!is_dir($storagePath)) {
    echo "⚠️  WARNING: Storage path not found: $storagePath\n";
    echo "   Run: php artisan storage:link\n\n";
} else {
    echo "✅ PASS: Storage is configured\n";
    echo "   Path: $storagePath\n\n";
}

// 3. Test API connection (optional - requires credits)
echo "Would you like to test the API connection? This will use a small amount of credits.\n";
echo "Type 'yes' to continue or press Enter to skip: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'yes') {
    echo "\nTesting API connection...\n";
    
    try {
        $service = new OpenAIService();
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->timeout(10)->get('https://api.openai.com/v1/models');

        if ($response->successful()) {
            echo "✅ SUCCESS: API connection working!\n";
            echo "   Available models: " . count($response->json()['data']) . "\n";
            
            // Check if gpt-4o is available
            $models = collect($response->json()['data'])->pluck('id')->toArray();
            if (in_array('gpt-4o', $models)) {
                echo "✅ gpt-4o model is available\n";
            } else {
                echo "⚠️  gpt-4o not found in your available models\n";
            }
        } else {
            echo "❌ FAIL: API connection failed\n";
            echo "   Status: " . $response->status() . "\n";
            echo "   Error: " . $response->body() . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "Skipped API test.\n";
}

echo "\n=================================\n";
echo "Test Complete!\n";
echo "=================================\n";
echo "\nIf all tests pass, your OpenAI integration should work.\n";
echo "Check Laravel logs for detailed error messages:\n";
echo "  tail -f storage/logs/laravel.log\n\n";
