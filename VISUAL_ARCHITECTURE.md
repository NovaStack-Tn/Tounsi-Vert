# 🎨 Architecture Visuelle - Système de Reports IA

## 📐 Diagramme d'Architecture Globale

```
┌─────────────────────────────────────────────────────────────┐
│                    TOUNSI-VERT PLATFORM                      │
│                 Système de Reports Intelligent               │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              │                               │
      ┌───────▼────────┐            ┌────────▼────────┐
      │   FRONT-END    │            │    BACK-END     │
      │   (Blade)      │            │    (Laravel)    │
      └───────┬────────┘            └────────┬────────┘
              │                               │
  ┌───────────┼───────────┐       ┌──────────┼──────────┐
  │           │           │       │          │          │
┌─▼──┐   ┌───▼──┐   ┌───▼──┐  ┌─▼───┐  ┌───▼──┐  ┌───▼──┐
│Mem │   │Admin │   │Public│  │Model│  │Ctrl  │  │Service│
│ber │   │Views │   │Views │  │     │  │      │  │  IA  │
└─┬──┘   └───┬──┘   └───┬──┘  └──┬──┘  └───┬──┘  └───┬──┘
  │          │          │        │         │        │
  └──────────┴──────────┴────────┴─────────┴────────┘
                        │
              ┌─────────▼──────────┐
              │  BASE DE DONNÉES   │
              │  ┌──────────────┐  │
              │  │   reports    │  │
              │  │report_actions│  │
              │  └──────────────┘  │
              └────────────────────┘
```

## 🤖 Processus d'Analyse IA

```
INPUT → Pattern Detection → Score Calculation → Priority
  ↓           ↓                    ↓               ↓
Text      Spam: 33%           Max: 33%        HIGH
Content   Fraud: 0%           Risk: 33        
          Violence: 0%         Auto-flag: NO
```

## 🗄️ Structure Base de Données

```
users ──┐
        │
        ├──→ reports ←── organizations
        │       │
        │       └──→ report_actions
        │
        └──→ (resolved_by)
```

## 📊 Matrice de Décision IA

**Priority:**
- Score >= 40% + Violence/Fraud → CRITICAL 🔴
- Score >= 30% → HIGH 🟠
- Score >= 15% → MEDIUM 🟡
- Score < 15% → LOW 🟢

**Risk Level:**
- 80-100 → CRITICAL 🔴
- 60-79 → HIGH 🟠
- 40-59 → MEDIUM 🔵
- 0-39 → LOW 🟢

**Auto-Flag:**
- Violence >= 40% → YES
- Fraud >= 50% → YES
- Risk >= 85 → YES

---

**Version:** 3.0.0 (AI-Enhanced)  
**Date:** 2025-10-23
