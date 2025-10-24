# Test Gemini AI Report Analysis

## Test Cases

### Test 1: Spam Detection
**Input:**
- Reason: "Promotional content"
- Details: "This organization keeps posting buy now, click here, limited offer messages"

**Expected Output:**
- Suggested Category: spam
- Confidence: High (>70%)
- Priority: medium
- Risk Score: 40-60

### Test 2: Fraud Detection
**Input:**
- Reason: "Scam organization"
- Details: "This is a fake organization trying to steal donations through phishing"

**Expected Output:**
- Suggested Category: fraud
- Confidence: High (>80%)
- Priority: high or critical
- Risk Score: 70-90
- Auto-flag: true

### Test 3: Violence/Threats
**Input:**
- Reason: "Threatening behavior"
- Details: "The organizer made death threats and violent comments towards participants"

**Expected Output:**
- Suggested Category: violence
- Confidence: High (>85%)
- Priority: critical
- Risk Score: 85-100
- Auto-flag: true
- Requires immediate attention: true

### Test 4: Harassment
**Input:**
- Reason: "Harassment and bullying"
- Details: "Hate speech, discrimination, and racist comments in event description"

**Expected Output:**
- Suggested Category: harassment
- Confidence: High (>80%)
- Priority: high
- Risk Score: 70-85

### Test 5: Inappropriate Content
**Input:**
- Reason: "Inappropriate content"
- Details: "Event contains explicit, NSFW, and adult content"

**Expected Output:**
- Suggested Category: inappropriate
- Confidence: High (>75%)
- Priority: medium or high
- Risk Score: 60-75

### Test 6: Low Risk Report
**Input:**
- Reason: "Minor issue"
- Details: "The event time was changed without notice"

**Expected Output:**
- Suggested Category: other
- Confidence: Low-Medium (<60%)
- Priority: low
- Risk Score: 10-30
- Auto-flag: false

## Manual Testing Steps

### 1. Submit a Test Report
1. Login as a member
2. Navigate to an event or organization
3. Click "Report"
4. Fill in the form with test data
5. Submit the report

### 2. Verify AI Analysis
1. Login as admin
2. Go to Admin > Reports
3. Find the test report
4. Check for "Gemini" badge
5. Click to view details

### 3. Verify Analysis Details
Check that the report shows:
- ✅ "Gemini AI Analysis" badge
- ✅ Suggested category
- ✅ Confidence score
- ✅ Risk level
- ✅ Category scores breakdown
- ✅ Analysis summary (if Gemini succeeded)
- ✅ Recommended action (if Gemini succeeded)

### 4. Test Fallback
1. Temporarily remove/invalidate GEMINI_API_KEY
2. Submit a new report
3. Verify pattern matching fallback works
4. Check that "Gemini" badge is NOT shown
5. Verify `ai_powered` is false in database

### 5. Check Logs
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Look for:
# - "Gemini AI Report Analysis" (success)
# - "Gemini API key not configured" (missing key)
# - "Gemini API Exception" (API error)
```

## Database Verification

### Check Report Record
```sql
SELECT 
    id,
    reason,
    category,
    priority,
    ai_risk_score,
    ai_suggested_category,
    ai_confidence,
    ai_auto_flagged,
    ai_analysis
FROM reports
ORDER BY created_at DESC
LIMIT 1;
```

### Verify AI Analysis JSON
```sql
SELECT 
    id,
    JSON_EXTRACT(ai_analysis, '$.ai_powered') as ai_powered,
    JSON_EXTRACT(ai_analysis, '$.suggested_category') as suggested_category,
    JSON_EXTRACT(ai_analysis, '$.analysis_summary') as summary,
    JSON_EXTRACT(ai_analysis, '$.recommended_action') as action
FROM reports
WHERE ai_analysis IS NOT NULL
ORDER BY created_at DESC
LIMIT 5;
```

## API Testing

### Test Gemini API Directly
```bash
# Using curl (replace YOUR_API_KEY)
curl -X POST \
  'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=YOUR_API_KEY' \
  -H 'Content-Type: application/json' \
  -d '{
    "contents": [{
      "parts": [{
        "text": "Analyze this report: Reason: Spam. Details: Buy now, click here, limited offer"
      }]
    }]
  }'
```

### Test via Tinker
```bash
php artisan tinker
```

```php
// In tinker
use App\Services\ReportAnalysisService;

$service = new ReportAnalysisService();

// Test analysis
$result = $service->analyzeReportContent(
    "Spam content",
    "This organization posts buy now, click here, free money messages"
);

print_r($result);

// Check if Gemini was used
echo $result['ai_powered'] ? "Gemini AI used" : "Pattern matching used";
```

## Performance Testing

### Response Time
```php
$start = microtime(true);
$result = $service->analyzeReportContent($reason, $details);
$duration = microtime(true) - $start;

echo "Analysis took: " . round($duration, 2) . " seconds\n";
```

**Expected:** 1-3 seconds for Gemini, <0.1 seconds for pattern matching

### Load Testing
```bash
# Create 10 test reports
for i in {1..10}; do
    # Submit report via API or form
    echo "Creating report $i"
done

# Check all were analyzed
php artisan tinker
>>> App\Models\Report::whereNotNull('ai_analysis')->count();
```

## Troubleshooting

### Issue: No Gemini badge shown
**Check:**
1. Is GEMINI_API_KEY set in .env?
2. Check logs for API errors
3. Verify ai_analysis JSON contains 'ai_powered' => true

### Issue: Low confidence scores
**Possible causes:**
1. Vague report content
2. Mixed signals in text
3. Normal behavior - not all reports are clear violations

### Issue: API timeout
**Solutions:**
1. Check internet connection
2. Verify API key is valid
3. Check Gemini API status
4. System will fallback to pattern matching

### Issue: Wrong category suggested
**Actions:**
1. Review the analysis_summary
2. Check if content is ambiguous
3. Admin can manually override category
4. Consider improving prompt in future

## Success Criteria

✅ **Integration successful if:**
- Reports are created without errors
- AI analysis is stored in database
- Gemini badge appears on analyzed reports
- Analysis summary and recommendations are shown
- Fallback works when API is unavailable
- No performance degradation
- Logs show successful API calls

## Next Steps After Testing

1. Monitor API usage and costs
2. Collect feedback from admins
3. Adjust confidence thresholds if needed
4. Consider caching for similar reports
5. Implement async processing for high volume
6. Add analytics dashboard for AI performance

---

**Test Date**: _____________
**Tester**: _____________
**Results**: _____________
**Issues Found**: _____________
**Status**: ⬜ Pass ⬜ Fail ⬜ Needs Review
