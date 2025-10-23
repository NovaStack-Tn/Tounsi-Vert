# 🤖 Gemini AI Integration - Setup Guide

**Status:** ✅ Integrated  
**AI Provider:** Google Gemini Pro  
**Purpose:** Intelligent insights, predictions, and recommendations

---

## 🎯 What Gemini AI Does

### **1. Dashboard Insights** 
- Analyzes platform health
- Provides intelligent recommendations
- Predicts trends and growth
- Generates actionable alerts

### **2. Organization Analysis**
- Quality score calculation
- Anomaly detection
- Strength/weakness analysis
- Improvement recommendations

### **3. Event Recommendations**
- Personalized event suggestions for users
- AI-powered matching based on history
- Smart confidence scoring

### **4. Event Predictions**
- Predicts participation numbers
- Analyzes success factors
- Provides confidence levels

---

## 🔑 Step 1: Get Gemini API Key

### **Get Free API Key:**

1. **Go to Google AI Studio:**
   ```
   https://makersuite.google.com/app/apikey
   ```

2. **Sign in with Google Account**

3. **Click "Create API Key"**

4. **Select "Create API key in new project"** or use existing project

5. **Copy your API key** (looks like: `AIzaSyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`)

### **Important Notes:**
- ✅ **Free tier:** 60 requests per minute
- ✅ **No credit card required**
- ✅ **Generous free quota**

---

## ⚙️ Step 2: Configure Your Project

### **Add to `.env` file:**

Open `backend/.env` and add:

```env
# Gemini AI Configuration
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=gemini-pro
```

### **Example:**
```env
GEMINI_API_KEY=AIzaSyABCDEFGHIJKLMNOPQRSTUVWXYZ123456
GEMINI_MODEL=gemini-pro
```

---

## 📁 Step 3: Files Created/Modified

### **✅ Files Created:**
1. `app/Services/GeminiAIService.php` - Main Gemini integration service
2. `GEMINI_AI_SETUP.md` - This setup guide

### **✅ Files Modified:**
3. `config/services.php` - Added Gemini configuration
4. `app/Http/Controllers/Admin/AIController.php` - Uses Gemini when available

### **✅ Automatic Fallback:**
- If no API key → Uses basic `TounsiVertAIService`
- If API key present → Uses advanced `GeminiAIService`

---

## 🚀 Step 4: Test the Integration

### **1. Clear Config Cache:**
```bash
cd backend
php artisan config:clear
php artisan cache:clear
```

### **2. Access AI Dashboard:**
```
http://localhost:8000/admin/ai/dashboard
```

### **3. What You'll See:**

```
┌────────────────────────────────────────┐
│ 🤖 Dashboard Intelligence Artificielle │
├────────────────────────────────────────┤
│                                        │
│ Santé de la Plateforme                │
│ Score: 85  |  Status: Good            │
│                                        │
│ Événements Tendance                    │
│ 1. Beach Cleanup - 45 participants    │
│ 2. Tree Planting - 38 participants    │
│                                        │
│ Top Organisations                      │
│ 🏆 Green Earth - Score: 92            │
│ 🥇 Eco Warriors - Score: 78           │
│                                        │
│ Prédictions IA                         │
│ Next month events: 25                  │
│ Confidence: 85%                        │
└────────────────────────────────────────┘
```

---

## 💡 Features Explained

### **1. Platform Health Analysis**

**What Gemini Does:**
- Analyzes user growth, event trends, participation rates
- Provides health score (0-100)
- Suggests improvements
- Detects potential issues

**Data Sent to Gemini:**
```
- Active users (30 days)
- Upcoming events
- Recent participations
- Growth rates
```

**Gemini Response:**
```json
{
  "health_score": 85,
  "status": "good",
  "insights": [
    "Strong user engagement",
    "Growing event participation"
  ],
  "recommendations": [
    "Focus on user retention",
    "Promote more events"
  ]
}
```

---

### **2. Organization Analysis**

**What Gemini Does:**
- Evaluates organization performance
- Identifies strengths and weaknesses
- Calculates risk level
- Provides actionable suggestions

**Example Analysis:**
```json
{
  "strengths": [
    "High event completion rate",
    "Excellent user ratings",
    "Strong donation support"
  ],
  "weaknesses": [
    "Low profile completeness",
    "Limited social media presence"
  ],
  "risk_level": "low",
  "overall_assessment": "Reliable organization with room for improvement"
}
```

---

### **3. Event Recommendations**

**How It Works:**
1. Collects user's participation history
2. Analyzes favorite categories
3. Finds similar upcoming events
4. Gemini ranks and explains recommendations

**Example Recommendation:**
```json
{
  "event_id": 42,
  "score": 95,
  "reason": "Matches your interest in environmental cleanup events and the organization has high ratings"
}
```

---

### **4. Event Participation Prediction**

**What Gemini Analyzes:**
- Event details (type, category, location)
- Organization reputation
- Similar past events
- Days until event
- Current trend

**Prediction Output:**
```json
{
  "predicted_participants": 65,
  "confidence": 82,
  "key_factors": [
    "Popular organization",
    "High-demand category",
    "Optimal timing"
  ]
}
```

---

## 🔒 Security & Privacy

### **Data Sent to Gemini:**
- ✅ Aggregated statistics only
- ✅ NO personal user data (names, emails, passwords)
- ✅ NO sensitive information
- ✅ Only counts, averages, and trends

### **What's Shared:**
- Event titles and categories
- Organization names (public info)
- Participation counts
- Rating averages
- Growth percentages

### **What's NOT Shared:**
- ❌ User emails, passwords, addresses
- ❌ Personal identification
- ❌ Payment information
- ❌ Private messages

---

## 🎨 Customizing Gemini Responses

### **Adjust Temperature** (Creativity):

Edit `app/Services/GeminiAIService.php`:

```php
'generationConfig' => [
    'temperature' => 0.7,  // 0.0 = precise, 1.0 = creative
    'topK' => 40,
    'topP' => 0.95,
    'maxOutputTokens' => 1024,
]
```

**Temperature Guide:**
- `0.0-0.3` - Very factual, consistent
- `0.4-0.7` - Balanced (recommended)
- `0.8-1.0` - Creative, varied

---

## 🧪 Testing Gemini

### **Test 1: Check API Key**
```bash
curl "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=YOUR_API_KEY" \
  -H 'Content-Type: application/json' \
  -X POST \
  -d '{"contents":[{"parts":[{"text":"Hello Gemini!"}]}]}'
```

### **Test 2: Check Laravel Logs**
```bash
tail -f storage/logs/laravel.log
```

If Gemini fails, you'll see error logs here.

### **Test 3: Verify Fallback**
Remove API key temporarily:
```env
# GEMINI_API_KEY=  # commented out
```

Dashboard should still work with basic AI.

---

## 🐛 Troubleshooting

### **Problem: "API Key not found"**

**Solution:**
```bash
# 1. Check .env file
cat backend/.env | grep GEMINI

# 2. Clear config
php artisan config:clear

# 3. Restart server
php artisan serve
```

---

### **Problem: "Rate limit exceeded"**

**Gemini Free Tier Limits:**
- 60 requests per minute
- 1,500 requests per day

**Solution:**
```php
// Add caching in GeminiAIService.php
Cache::remember("ai_insights", 3600, function() {
    return $this->callGemini($prompt);
});
```

---

### **Problem: "Timeout error"**

**Solution:** Increase timeout in service:

```php
$response = Http::timeout(60)->post(...);  // 60 seconds
```

---

### **Problem: "Invalid JSON response"**

**Cause:** Gemini sometimes returns markdown-formatted JSON

**Solution:** Already handled in `parseAIResponse()` method

---

## 📊 Monitoring Gemini Usage

### **Check Request Count:**

```php
// In controller
Log::info('Gemini API called', [
    'endpoint' => 'dashboard',
    'timestamp' => now(),
]);
```

### **View Logs:**
```bash
tail -f storage/logs/laravel.log | grep "Gemini"
```

---

## 💰 Pricing (As of 2024)

### **Gemini Pro - Free Tier:**
- ✅ **60 requests/minute**
- ✅ **1,500 requests/day**
- ✅ **Sufficient for most use cases**
- ✅ **No credit card required**

### **If You Need More:**
- Gemini Pro: $0.00025 per 1K characters input
- Gemini Pro: $0.0005 per 1K characters output
- Very affordable for production

---

## 🎯 Usage Recommendations

### **For Development:**
- Use API key
- Test all AI features
- Monitor logs

### **For Production:**
- ✅ Use caching to reduce API calls
- ✅ Set reasonable timeouts
- ✅ Implement fallback logic (already done)
- ✅ Monitor usage in Google Cloud Console

---

## 📝 API Methods Available

| Method | Description | Returns |
|--------|-------------|---------|
| `generateAIDashboardInsights()` | Platform overview | Platform metrics + AI insights |
| `analyzeOrganization($org)` | Organization analysis | Quality score + recommendations |
| `recommendEventsForUser($user)` | Personalized recommendations | Top 10 events with scores |
| `predictEventParticipation($event)` | Participation prediction | Predicted count + confidence |
| `detectOrganizationAnomalies($org)` | Fraud detection | Anomaly list |
| `calculateOrganizationQualityScore($org)` | Quality scoring | Score 0-100 |

---

## 🔄 Switching Between AI Services

### **Use Gemini (Recommended):**
```env
GEMINI_API_KEY=your_key_here
```

### **Use Basic AI (Fallback):**
```env
# GEMINI_API_KEY=  # remove or comment out
```

Controller automatically detects and switches!

---

## ✨ Example Prompts Used

### **Dashboard Analysis Prompt:**
```
You are an AI analyst for Tounsi-Vert, an environmental platform.

Analyze these metrics:
- Active Users (30 days): 150 out of 500 total
- Upcoming Events: 25 out of 120 total
- Recent Participations: 85
- User Growth Rate: 15%

Provide JSON with health_score, status, insights, recommendations
```

### **Event Recommendation Prompt:**
```
Recommend environmental events for this user:

User Profile:
- Past participations: 8
- Favorite categories: Beach Cleanup, Tree Planting
- Average rating: 4.5/5

Available Events:
- Beach Cleanup at Hammamet (50 participants)
- Forest Conservation Workshop (30 participants)

Recommend top 5 with scores and reasons.
```

---

## 🚀 Next Steps

1. **✅ Get API Key** from Google AI Studio
2. **✅ Add to `.env`** file
3. **✅ Clear config** cache
4. **✅ Test dashboard** at `/admin/ai/dashboard`
5. **✅ Monitor** logs for errors
6. **✅ Enjoy** intelligent insights!

---

## 📚 Resources

- **Google AI Studio:** https://makersuite.google.com
- **Gemini API Docs:** https://ai.google.dev/docs
- **Free API Key:** https://makersuite.google.com/app/apikey
- **Pricing:** https://ai.google.dev/pricing

---

## ✅ Checklist

- [ ] Got Gemini API key from Google AI Studio
- [ ] Added `GEMINI_API_KEY` to `.env`
- [ ] Cleared config cache (`php artisan config:clear`)
- [ ] Tested AI dashboard
- [ ] Verified logs for errors
- [ ] Set up caching (optional)
- [ ] Configured monitoring (optional)

---

**🎉 Gemini AI is ready! Your platform now has intelligent insights powered by Google's latest AI model.**

**Questions? Check the troubleshooting section or Laravel logs.**

---

**Last Updated:** October 23, 2025  
**Version:** 1.0.0  
**Status:** 🟢 Production Ready
