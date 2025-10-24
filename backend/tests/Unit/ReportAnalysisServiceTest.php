<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ReportAnalysisService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportAnalysisServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReportAnalysisService();
    }

    /** @test */
    public function it_analyzes_spam_content_with_pattern_matching()
    {
        // Mock Gemini API to fail, forcing pattern matching
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Spam content',
            'Buy now, click here, limited offer, free money'
        );

        $this->assertIsArray($result);
        $this->assertEquals('spam', $result['suggested_category']);
        $this->assertGreaterThan(0, $result['risk_score']);
        $this->assertFalse($result['ai_powered']);
    }

    /** @test */
    public function it_analyzes_fraud_content_with_pattern_matching()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Fraud alert',
            'This is a scam, fake organization, phishing attempt, trying to steal money'
        );

        $this->assertEquals('fraud', $result['suggested_category']);
        $this->assertGreaterThan(50, $result['risk_score']);
        $this->assertTrue($result['auto_flag']);
    }

    /** @test */
    public function it_analyzes_violence_content_with_pattern_matching()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Violent threats',
            'Death threat, violence, attack, assault, dangerous weapon'
        );

        $this->assertEquals('violence', $result['suggested_category']);
        $this->assertEquals('critical', $result['priority']);
        $this->assertGreaterThan(70, $result['risk_score']);
    }

    /** @test */
    public function it_analyzes_harassment_content_with_pattern_matching()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Harassment report',
            'Bullying, hate speech, discrimination, racist comments, stalking'
        );

        $this->assertEquals('harassment', $result['suggested_category']);
        $this->assertGreaterThan(40, $result['risk_score']);
    }

    /** @test */
    public function it_analyzes_inappropriate_content_with_pattern_matching()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Inappropriate content',
            'Explicit, NSFW, adult content, pornographic material'
        );

        $this->assertEquals('inappropriate', $result['suggested_category']);
        $this->assertGreaterThan(30, $result['risk_score']);
    }

    /** @test */
    public function it_handles_low_risk_content()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Minor issue',
            'The event time was changed without notice'
        );

        $this->assertLessThan(30, $result['risk_score']);
        $this->assertEquals('low', $result['priority']);
        $this->assertFalse($result['auto_flag']);
    }

    /** @test */
    public function it_returns_required_fields()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Test reason',
            'Test details'
        );

        $this->assertArrayHasKey('suggested_category', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertArrayHasKey('priority', $result);
        $this->assertArrayHasKey('risk_score', $result);
        $this->assertArrayHasKey('category_scores', $result);
        $this->assertArrayHasKey('requires_immediate_attention', $result);
        $this->assertArrayHasKey('auto_flag', $result);
        $this->assertArrayHasKey('ai_powered', $result);
    }

    /** @test */
    public function it_validates_priority_values()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Test',
            'Test content'
        );

        $this->assertContains($result['priority'], ['low', 'medium', 'high', 'critical']);
    }

    /** @test */
    public function it_validates_risk_score_range()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Test',
            'Test content'
        );

        $this->assertGreaterThanOrEqual(0, $result['risk_score']);
        $this->assertLessThanOrEqual(100, $result['risk_score']);
    }

    /** @test */
    public function it_validates_confidence_range()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Test',
            'Test content'
        );

        $this->assertGreaterThanOrEqual(0, $result['confidence']);
        $this->assertLessThanOrEqual(100, $result['confidence']);
    }

    /** @test */
    public function it_auto_flags_high_risk_content()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent(
            'Critical violation',
            'Violence, death threat, fraud, scam, dangerous weapon, attack'
        );

        $this->assertTrue($result['auto_flag']);
        $this->assertTrue($result['requires_immediate_attention']);
    }

    /** @test */
    public function it_uses_gemini_when_api_key_is_configured()
    {
        // Set a test API key
        config(['services.gemini.api_key' => 'test_key_123']);

        // Mock successful Gemini API response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'suggested_category' => 'spam',
                                        'confidence' => 85,
                                        'priority' => 'medium',
                                        'risk_score' => 65,
                                        'category_scores' => [
                                            'spam' => 85,
                                            'inappropriate' => 10,
                                            'fraud' => 5,
                                            'harassment' => 0,
                                            'violence' => 0,
                                        ],
                                        'requires_immediate_attention' => false,
                                        'auto_flag' => false,
                                        'analysis_summary' => 'This appears to be spam content',
                                        'recommended_action' => 'Review and remove if confirmed',
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->analyzeReportContent(
            'Spam test',
            'Buy now, click here'
        );

        $this->assertTrue($result['ai_powered']);
        $this->assertEquals('spam', $result['suggested_category']);
        $this->assertEquals(85, $result['confidence']);
    }

    /** @test */
    public function it_falls_back_to_pattern_matching_when_gemini_fails()
    {
        config(['services.gemini.api_key' => 'test_key_123']);

        // Mock failed Gemini API response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500)
        ]);

        $result = $this->service->analyzeReportContent(
            'Spam content',
            'Buy now, click here'
        );

        // Should fallback to pattern matching
        $this->assertFalse($result['ai_powered']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('suggested_category', $result);
    }

    /** @test */
    public function it_logs_gemini_analysis_success()
    {
        config(['services.gemini.api_key' => 'test_key_123']);

        Log::shouldReceive('info')
            ->once()
            ->with('Gemini AI Report Analysis', \Mockery::type('array'));

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'suggested_category' => 'spam',
                                        'confidence' => 85,
                                        'priority' => 'medium',
                                        'risk_score' => 65,
                                        'category_scores' => [],
                                        'requires_immediate_attention' => false,
                                        'auto_flag' => false,
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $this->service->analyzeReportContent('Test', 'Test');
    }

    /** @test */
    public function it_handles_empty_content()
    {
        config(['services.gemini.api_key' => null]);

        $result = $this->service->analyzeReportContent('', '');

        $this->assertIsArray($result);
        $this->assertEquals('low', $result['priority']);
        $this->assertLessThan(20, $result['risk_score']);
    }

    /** @test */
    public function it_handles_very_long_content()
    {
        config(['services.gemini.api_key' => null]);

        $longContent = str_repeat('This is a test content. ', 200);

        $result = $this->service->analyzeReportContent(
            'Long report',
            $longContent
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('suggested_category', $result);
    }
}
