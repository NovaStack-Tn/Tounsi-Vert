# Gemini AI Report Analysis Integration

## Overview
The report analysis system has been upgraded to use **Google Gemini AI** for intelligent content moderation and risk assessment.

## Features

### 🤖 AI-Powered Analysis
- **Gemini AI Integration**: Uses Google's Gemini Pro model for advanced content analysis
- **Automatic Fallback**: Falls back to pattern matching if Gemini API is unavailable
- **Smart Detection**: Identifies violations across 8 categories:
  - Spam
  - Inappropriate Content
  - Fraud/Scam
  - Harassment
  - Violence
  - Misinformation
  - Copyright Violations
  - Other Policy Violations

### 📊 Analysis Output
When a report is submitted, Gemini AI provides:
- **Suggested Category**: Most likely violation type
- **Confidence Score**: 0-100% confidence in the analysis
- **Priority Level**: low, medium, high, or critical
- **Risk Score**: Overall risk assessment (0-100)
- **Category Scores**: Breakdown of scores for each violation type
- **Analysis Summary**: Human-readable explanation of the violation
- **Recommended Action**: Suggested moderation action
- **Auto-Flag**: Whether the report should be immediately flagged

### 🎯 Visual Indicators
Reports analyzed by Gemini AI display:
- ✨ **"Gemini AI Analysis"** badge in the report details
- 🌟 **"Gemini"** badge in the reports list
- 💡 **Analysis Summary** with AI insights
- ⚠️ **Recommended Action** suggestions

## Configuration

### 1. API Key Setup
Add your Gemini API key to `.env`:
```env
GEMINI_API_KEY=your_api_key_here
```

### 2. Service Configuration
The Gemini service is configured in `config/services.php`:
```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-pro'),
],
```

## How It Works

### Analysis Flow
1. **User submits a report** via the member portal
2. **ReportAnalysisService** receives the report content
3. **Gemini AI analyzes** the reason and details
4. **AI returns** structured JSON with analysis results
5. **System stores** the analysis in the database
6. **Admins view** the AI insights in the admin panel

### Code Structure

#### ReportAnalysisService.php
```php
// Main analysis method
public function analyzeReportContent(string $reason, string $details): array

// Gemini AI analysis
protected function analyzeWithGemini(string $reason, string $details): ?array

// Fallback pattern matching
protected function analyzeWithPatternMatching(string $reason, string $details): array
```

#### Database Fields
Reports table includes AI-related fields:
- `ai_risk_score` (integer): 0-100 risk score
- `ai_suggested_category` (string): Suggested violation category
- `ai_confidence` (decimal): Confidence percentage
- `ai_auto_flagged` (boolean): Auto-flagged status
- `ai_analysis` (JSON): Full analysis results

## Usage Examples

### For Admins
1. Navigate to **Admin > Reports**
2. View reports with Gemini badge
3. Click on a report to see detailed AI analysis
4. Review the **Gemini Analysis** section showing:
   - Suggested category
   - Confidence score
   - Risk level
   - Category breakdown
   - AI summary and recommendations

### For Developers
```php
use App\Services\ReportAnalysisService;

$service = new ReportAnalysisService();
$analysis = $service->analyzeReportContent(
    "This organization is posting spam",
    "They keep promoting products unrelated to environmental causes"
);

// Returns:
// [
//     'suggested_category' => 'spam',
//     'confidence' => 92,
//     'priority' => 'medium',
//     'risk_score' => 65,
//     'category_scores' => [...],
//     'requires_immediate_attention' => false,
//     'auto_flag' => false,
//     'analysis_summary' => 'The report indicates...',
//     'recommended_action' => 'Review the organization...',
//     'ai_powered' => true
// ]
```

## Benefits

### 🎯 Accuracy
- More accurate violation detection than pattern matching
- Context-aware analysis
- Understands nuanced language

### ⚡ Efficiency
- Automatic prioritization
- Instant risk assessment
- Reduces manual review time

### 🛡️ Safety
- Proactive threat detection
- Auto-flagging of critical violations
- Consistent moderation standards

### 📈 Insights
- Detailed analysis summaries
- Actionable recommendations
- Confidence scoring

## Fallback Mechanism

If Gemini API is unavailable or fails:
1. System automatically falls back to pattern matching
2. Analysis continues without interruption
3. `ai_powered` flag is set to `false`
4. Logs the failure for monitoring

## API Rate Limits

Gemini API has usage limits:
- Free tier: 60 requests per minute
- Consider implementing caching for repeated analyses
- Monitor API usage in logs

## Troubleshooting

### API Key Issues
```
Error: Gemini API key not configured
Solution: Add GEMINI_API_KEY to .env file
```

### API Failures
```
Error: Gemini API Exception
Solution: Check logs in storage/logs/laravel.log
Fallback: System uses pattern matching automatically
```

### JSON Parsing Errors
```
Error: Failed to parse Gemini response
Solution: Check API response format in logs
Fallback: Returns null, triggers pattern matching
```

## Future Enhancements

- [ ] Batch analysis for multiple reports
- [ ] Custom training for Tunisian context
- [ ] Multi-language support (Arabic, French)
- [ ] Historical analysis trends
- [ ] Automated moderation actions
- [ ] Integration with notification system

## Security Considerations

- API key stored securely in `.env`
- Never expose API key in client-side code
- Validate and sanitize all AI responses
- Log all AI decisions for audit trail
- Human review required for critical actions

## Performance

- Average analysis time: 1-3 seconds
- Timeout: 30 seconds
- Caching: Not implemented (consider for future)
- Async processing: Consider for high volume

## Monitoring

Monitor these metrics:
- Gemini API success rate
- Fallback usage frequency
- Average confidence scores
- Auto-flag accuracy
- Response times

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review Gemini API status
3. Test with pattern matching fallback
4. Contact development team

---

**Last Updated**: 2025-10-24
**Version**: 1.0.0
**Author**: Tounsi-Vert Development Team
