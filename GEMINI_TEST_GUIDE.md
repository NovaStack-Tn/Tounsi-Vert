# 🧪 Gemini AI - Testing Guide

## 🎯 How to Verify Gemini is Working

---

## ✅ Method 1: Visual Check (Easiest)

### **Step 1: Open Dashboard**
```
http://localhost:8000/admin/ai/dashboard
```

### **Step 2: Look for Status Indicators**

**If Gemini is NOT configured:**
```
┌────────────────────────────────────────┐
│ Powered by Google Gemini AI            │
│ [⚠ Not Configured]                     │
└────────────────────────────────────────┘

⚠️ Gemini API Not Configured
(Shows setup instructions)
```

**If Gemini IS working:**
```
┌────────────────────────────────────────┐
│ Powered by Google Gemini AI            │
│ [✓ Active]                             │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│ 🤖 Gemini AI Status                    │
│ ✓ API Key: Configured                  │
│ ✓ Model: gemini-pro                    │
│ ✓ AI Insights: Active                  │
│        ⭐                               │
│ Powered by Google Gemini               │
└────────────────────────────────────────┘
```

---

## ✅ Method 2: Check Laravel Logs

### **Step 1: Clear Old Logs (Optional)**
```bash
cd backend
echo "" > storage/logs/laravel.log
```

### **Step 2: Visit Dashboard**
```
http://localhost:8000/admin/ai/dashboard
```

### **Step 3: Check Logs**
```bash
tail -f storage/logs/laravel.log
```

**If Gemini is working, you'll see:**
```
[timestamp] local.INFO: Gemini AI Dashboard Analysis
{
    "api_key_configured": true,
    "response_length": 245,
    "timestamp": "2025-10-23 22:50:00"
}
```

**If API key is missing:**
```
[timestamp] local.ERROR: Gemini API key is missing. Please configure GEMINI_API_KEY in your .env file.
```

**If API call fails:**
```
[timestamp] local.ERROR: Gemini API Error
{
    "response": "..."
}
```

---

## ✅ Method 3: Test API Key Directly

### **Quick API Test (PowerShell):**

```powershell
$apiKey = "YOUR_API_KEY_HERE"
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey"
$body = @{
    contents = @(
        @{
            parts = @(
                @{ text = "Say hello!" }
            )
        }
    )
} | ConvertTo-Json -Depth 10

Invoke-WebRequest -Uri $url -Method POST -ContentType "application/json" -Body $body
```

**Expected Response (if working):**
```json
{
  "candidates": [
    {
      "content": {
        "parts": [
          {
            "text": "Hello! How can I help you today?"
          }
        ]
      }
    }
  ]
}
```

**If API key is invalid:**
```
400 Bad Request
API key not valid
```

---

## ✅ Method 4: Compare Insights

### **Without Gemini (Fallback):**
Platform health shows basic stats:
- Score: Random or calculated number
- Status: Based on simple logic
- No detailed insights

### **With Gemini (AI-Powered):**
Platform health shows:
- Score: AI-analyzed (0-100)
- Status: AI assessment
- AI insights array with recommendations
- Predictions with confidence scores

---

## 🧪 Step-by-Step Full Test

### **Test 1: Without API Key**

1. **Remove API key from `.env`:**
   ```bash
   # Comment out in backend/.env:
   # GEMINI_API_KEY=
   ```

2. **Clear cache:**
   ```bash
   php artisan config:clear
   ```

3. **Visit dashboard:**
   ```
   http://localhost:8000/admin/ai/dashboard
   ```

4. **Expected Result:**
   - Orange "Not Configured" badge
   - Setup instructions visible
   - No "Gemini AI Status" card
   - Basic platform data only

---

### **Test 2: With API Key**

1. **Add API key to `.env`:**
   ```env
   GEMINI_API_KEY=your_actual_key_here
   GEMINI_MODEL=gemini-pro
   ```

2. **Clear cache:**
   ```bash
   php artisan config:clear
   ```

3. **Visit dashboard:**
   ```
   http://localhost:8000/admin/ai/dashboard
   ```

4. **Expected Result:**
   - Green "Active" badge
   - "Gemini AI Status" card visible
   - Shows: API Key Configured, Model, AI Insights Active
   - Platform health score from Gemini
   - AI-generated insights

---

## 🔍 What to Look For

### **Gemini IS Working:**
✅ Green "Active" badge  
✅ "Gemini AI Status" card appears  
✅ Log shows: "Gemini AI Dashboard Analysis"  
✅ `ai_insights` key exists in response  
✅ Health score varies (AI-calculated)  
✅ Detailed recommendations  

### **Gemini NOT Working:**
⚠️ Orange "Not Configured" badge  
⚠️ Setup instructions shown  
⚠️ No status card  
⚠️ Log shows: "API key missing"  
⚠️ Basic fallback data only  

---

## 📊 Example Comparison

### **WITHOUT Gemini:**
```
Platform Health Score: 85
Status: good
Insights: (basic calculations)
```

### **WITH Gemini:**
```
Platform Health Score: 87 (AI-analyzed)
Status: excellent
Insights:
- "Strong user engagement detected"
- "Event participation trending upward"
- "Organization quality is improving"
Recommendations:
- "Focus on user retention programs"
- "Increase event variety in popular categories"
```

---

## 🐛 Troubleshooting

### **Problem: Always shows "Not Configured"**

**Check:**
```bash
# 1. Verify .env file
cat backend/.env | grep GEMINI

# 2. Should show:
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-pro

# 3. Clear cache
php artisan config:clear

# 4. Restart server
# Press Ctrl+C to stop
php artisan serve
```

---

### **Problem: Shows "Active" but no AI insights**

**Check logs:**
```bash
tail -n 50 storage/logs/laravel.log
```

**Look for:**
- API errors
- Timeout messages
- Invalid JSON responses

**Common fixes:**
- Invalid API key → Get new one
- Rate limit → Wait 1 minute
- Network issue → Check internet connection

---

### **Problem: API key not working**

**Verify key is valid:**
1. Go to: https://makersuite.google.com/app/apikey
2. Check if key is enabled
3. Try creating a new key
4. Copy exactly (no spaces)

---

## 🎯 Quick Verification Checklist

- [ ] Dashboard shows green "Active" badge
- [ ] "Gemini AI Status" card visible
- [ ] Platform health score displays
- [ ] Logs show "Gemini AI Dashboard Analysis"
- [ ] No errors in Laravel logs
- [ ] API key is in `.env` file
- [ ] Config cache is cleared

---

## 💡 Pro Tips

### **Tip 1: Enable Debug Mode**

In `.env`:
```env
APP_DEBUG=true
```

This shows detailed error messages.

### **Tip 2: Monitor API Calls**

Check Google Cloud Console:
```
https://console.cloud.google.com
→ Select your project
→ APIs & Services
→ Dashboard
→ See API usage
```

### **Tip 3: Test API Key Separately**

Use online tool:
```
https://aistudio.google.com/app/prompts/new_chat
```
Test if your API key works there first.

---

## ✅ Success Indicators

**You know Gemini is working when you see ALL of these:**

1. ✅ Green "Active" badge in header
2. ✅ Purple "Gemini AI Status" card
3. ✅ Shows "API Key: Configured"
4. ✅ Shows "AI Insights: Active" (not "Fallback Mode")
5. ✅ Laravel logs show Gemini API calls
6. ✅ No error messages in logs

---

## 📝 Current Status Check

Run this to see your current setup:

```bash
cd backend

# Check if API key is configured
php -r "require 'vendor/autoload.php'; 
        \$app = require_once 'bootstrap/app.php'; 
        \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); 
        echo 'Gemini API Key: ' . (config('services.gemini.api_key') ? 'Configured ✓' : 'Missing ✗') . PHP_EOL;
        echo 'Model: ' . config('services.gemini.model', 'Not set') . PHP_EOL;"
```

---

**If everything looks good, you're successfully using Gemini AI!** 🎉

**Still having issues?** Check `GEMINI_AI_SETUP.md` for detailed troubleshooting.
