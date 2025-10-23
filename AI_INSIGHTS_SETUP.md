# 🤖 AI Insights Setup Guide

## Overview

The AI Insights feature provides three intelligent cards for Organizers:

1. **🤖 AI Donation Insights** - Automatic analysis of 90-day donation trends
2. **🎯 Next-Best Actions** - 3 actionable recommendations for campaigns
3. **💚 Thank-You Template** - French thank-you message for donors

---

## 🚀 Quick Setup

### Step 1: Get API Keys

Choose ONE of the following AI providers:

#### Option A: OpenAI (Recommended)
1. Go to https://platform.openai.com/
2. Create an account or sign in
3. Navigate to API Keys section
4. Create a new API key
5. Copy the key (starts with `sk-...`)

#### Option B: Google Gemini
1. Go to https://makersuite.google.com/app/apikey
2. Sign in with Google account
3. Click "Create API Key"
4. Copy the API key

---

### Step 2: Configure Environment Variables

Open your `.env` file in the `backend` directory and add ONE of these:

**For OpenAI:**
```bash
OPENAI_API_KEY=sk-your-actual-api-key-here
```

**For Gemini:**
```bash
GEMINI_API_KEY=your-gemini-api-key-here
```

**Or both for redundancy:**
```bash
OPENAI_API_KEY=sk-your-openai-key
GEMINI_API_KEY=your-gemini-key
```

---

### Step 3: Clear Cache

```bash
cd backend
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 Testing

### 1. Login as Organizer
- Email: `organizer@tounsivert.tn`
- Password: `password`

### 2. Access AI Dashboard
Navigate to: **http://localhost:8000/organizer/ai**

Or click the **"🤖 AI Insights"** button on the organizer dashboard.

### 3. Verify Cards Display
✅ **AI Donation Insights** - Should show a paragraph in French  
✅ **Next-Best Actions** - Should show 3 bullet points  
✅ **Thank-You Template** - Should show a message with Copy button  

---

## 📁 Files Created

### Backend
- `app/Http/Controllers/Organizer/OrganizerAiController.php` - Main controller
- `resources/views/organizer/dashboard/ai.blade.php` - AI dashboard view
- `config/services.php` - Updated with API configs
- `routes/web.php` - Added `/organizer/ai` route

### Documentation
- `AI_INSIGHTS_SETUP.md` - This file

---

## 🎨 Features

### Card 1: AI Donation Insights
- Analyzes last 90 days of donations
- Summarizes amount, frequency, motivation
- Displays in French
- Fallback for no API key

### Card 2: Next-Best Actions
- Generates 3 actionable recommendations
- Context-aware based on donation data
- Refresh button to regenerate
- Smart fallback suggestions

### Card 3: Thank-You Template
- Warm French message for donors
- Copy to clipboard functionality
- Edit mode toggle
- Organization-specific content

---

## 🔐 Privacy & Security

✅ **No personal data shared** - Only aggregated statistics  
✅ **No donor names** - Only totals and trends  
✅ **Secure API calls** - HTTPS with token authentication  
✅ **Error handling** - Graceful fallbacks if API fails  

---

## 🛠️ Troubleshooting

### "Analyse IA indisponible pour le moment"
**Cause:** API key missing or invalid  
**Fix:** 
1. Check `.env` has correct API key
2. Run `php artisan config:clear`
3. Refresh the page

### AI responses are generic
**Cause:** Using fallback (no API key configured)  
**Fix:** Add a valid OpenAI or Gemini API key

### Copy button doesn't work
**Cause:** Browser doesn't support clipboard API  
**Fix:** Use a modern browser (Chrome, Firefox, Edge)

---

## 💰 API Costs

### OpenAI Pricing (gpt-3.5-turbo)
- ~$0.002 per page load (3 API calls)
- Very affordable for small/medium usage
- Free tier: $5 credit for new accounts

### Gemini Pricing
- Free tier available
- Generous limits for testing

---

## 🎯 Usage Tips

1. **Refresh regularly** - Click "Régénérer" for fresh insights
2. **Customize messages** - Use edit mode on thank-you template
3. **Track trends** - Check weekly for pattern changes
4. **Share insights** - Copy AI recommendations to team

---

## 📊 Data Used for AI

The AI uses only aggregated data:
- Total donation amount (90 days)
- Number of donations
- Average donation
- Monthly breakdown
- Organization name

**No individual donor information is ever sent to AI.**

---

## 🚀 Advanced Configuration

### Change AI Model (OpenAI)

Edit `OrganizerAiController.php`:

```php
'model' => 'gpt-4', // Instead of gpt-3.5-turbo
```

### Adjust Temperature (Creativity)

```php
'temperature' => 0.9, // 0.0 = precise, 1.0 = creative
```

### Change Time Period

```php
$startDate = Carbon::now()->subDays(180); // 180 days instead of 90
```

---

## ✅ Verification Checklist

- [ ] API key added to `.env`
- [ ] Config cache cleared
- [ ] Can access `/organizer/ai` route
- [ ] All 3 cards display
- [ ] AI content is in French
- [ ] Copy button works
- [ ] Refresh button works
- [ ] No errors in Laravel logs

---

## 📞 Support

If you encounter issues:
1. Check `storage/logs/laravel.log` for errors
2. Verify API key is valid
3. Test API key with a simple curl request
4. Ensure internet connection is active

---

## 🎉 Success Indicators

✅ Cards show French AI-generated content  
✅ Insights are specific to your organization  
✅ Actions are actionable and relevant  
✅ Thank-you message is warm and personal  

---

**Last Updated:** October 23, 2025  
**Status:** ✅ Production Ready  
**Author:** Tounsi-Vert Development Team
