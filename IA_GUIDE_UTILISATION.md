# 🤖 Guide d'Utilisation - Intelligence Artificielle Tounsi-Vert

## 📍 OÙ TROUVER L'IA?

L'Intelligence Artificielle est intégrée dans **plusieurs endroits** de la plateforme:

---

## 🎯 POUR LES ADMINISTRATEURS

### 1. Dashboard IA Principal
**URL:** `http://localhost:8000/admin/ai/dashboard`

**Comment y accéder:**
1. Connectez-vous comme admin (`admin@tounsivert.tn`)
2. Allez sur `/admin/ai/dashboard`

**Ce que vous y trouvez:**
- 📊 **Santé de la Plateforme** - Score global de santé
- 📈 **Événements Tendance** - Top 5 événements populaires
- 🏆 **Top Organisations** - Organisations avec meilleurs scores
- 👥 **Engagement Utilisateur** - Taux de participation et avis
- 🔮 **Prédictions** - Nombre d'événements prévus
- 🚨 **Alertes IA** - Alertes automatiques

### 2. Détection d'Anomalies
**URL:** `http://localhost:8000/admin/ai/anomalies`

**Comment y accéder:**
1. Dashboard Admin → Bouton "Anomalies"
2. Ou directement `/admin/ai/anomalies`

**Ce que vous y trouvez:**
- 🔍 Liste des organisations avec comportements suspects
- ⚠️ Types d'anomalies détectées
- 📊 Score de risque par organisation
- 💡 Recommandations IA

### 3. Analyse d'Organisation
**URL:** `http://localhost:8000/admin/ai/organization/{id}`

**Comment y accéder:**
1. Page d'anomalies → Bouton "Analyse Complète"
2. Ou directement avec l'ID de l'organisation

**Ce que vous y trouvez:**
- 🔬 Analyse détaillée d'une organisation
- 🏅 Score de qualité (0-100)
- 🎖️ Niveau (Bronze, Silver, Gold, Platinum)
- 🚩 Anomalies spécifiques

---

## 👤 POUR LES MEMBRES (USERS)

### 1. Recommandations Personnalisées
**URL:** `http://localhost:8000/member/ai/recommendations`

**Comment y accéder:**
1. Connectez-vous comme member
2. Menu → "Recommandations IA"
3. Ou allez sur `/member/ai/recommendations`

**Ce que vous y trouvez:**
- 🎯 **Événements recommandés** basés sur vos préférences
- 📍 **Localisation** - Événements dans votre région
- ⭐ **Score de match** - Pourcentage de compatibilité
- 💡 **Raisons** - Pourquoi cet événement vous est recommandé

---

## 🔧 FONCTIONNALITÉS IA DISPONIBLES

### 1. Recommandation d'Événements
```php
// Dans le code
$aiService = new TounsiVertAIService();
$recommendations = $aiService->recommendEventsForUser($user, 10);
```

**Critères utilisés:**
- ✅ Catégories favorites (40 points)
- ✅ Localisation (30 points)
- ✅ Popularité (20 points)
- ✅ Date (10 points)

### 2. Détection d'Anomalies
```php
$anomalies = $aiService->detectOrganizationAnomalies($organization);
```

**Anomalies détectées:**
- 🚨 Création excessive d'événements
- 📉 Taux d'annulation élevé
- 👥 Faible participation
- ⭐ Reviews négatives
- 💰 Donations suspectes

### 3. Prédiction de Participation
```php
$prediction = $aiService->predictEventParticipation($event);
```

**Facteurs pris en compte:**
- 📊 Historique de l'organisation
- 📂 Popularité de la catégorie
- 📍 Localisation
- 📅 Timing (jour de la semaine)
- 🌞 Saison

### 4. Analyse de Sentiment
```php
$sentiment = $aiService->analyzeSentiment($reviewText);
```

**Détecte:**
- 😊 Sentiment positif
- 😐 Sentiment neutre
- 😞 Sentiment négatif
- 📊 Score de confiance

### 5. Score de Qualité
```php
$quality = $aiService->calculateOrganizationQualityScore($organization);
```

**Critères évalués:**
- 📅 Nombre d'événements (20 pts)
- 👥 Participation moyenne (25 pts)
- ⭐ Reviews (25 pts)
- 💰 Donations (15 pts)
- 📆 Ancienneté (15 pts)

---

## 📱 COMMENT UTILISER L'IA?

### Exemple 1: Voir les Recommandations (Member)

```bash
# 1. Connectez-vous
http://localhost:8000/login
Email: member@tounsivert.tn
Password: password

# 2. Allez sur les recommandations
http://localhost:8000/member/ai/recommendations

# 3. Vous verrez:
- Liste d'événements personnalisés
- Score de match pour chaque événement
- Raisons de la recommandation
```

### Exemple 2: Dashboard IA (Admin)

```bash
# 1. Connectez-vous comme admin
http://localhost:8000/login
Email: admin@tounsivert.tn
Password: password

# 2. Accédez au dashboard IA
http://localhost:8000/admin/ai/dashboard

# 3. Explorez:
- Santé de la plateforme
- Événements tendance
- Top organisations
- Prédictions
```

### Exemple 3: Détecter les Anomalies (Admin)

```bash
# 1. Depuis le dashboard IA
Cliquez sur "Anomalies"

# 2. Ou directement
http://localhost:8000/admin/ai/anomalies

# 3. Vous verrez:
- Organisations suspectes
- Types d'anomalies
- Scores de risque
- Recommandations
```

---

## 🎨 INTÉGRATION DANS LES VUES

### Ajouter un Bouton de Recommandations

Dans votre navigation (ex: `layouts/app.blade.php`):

```blade
@auth
    <li class="nav-item">
        <a class="nav-link" href="{{ route('member.ai.recommendations') }}">
            <i class="bi bi-robot me-1"></i>Recommandations IA
        </a>
    </li>
@endauth
```

### Ajouter le Dashboard IA dans le Menu Admin

Dans `layouts/admin.blade.php`:

```blade
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.ai.dashboard') }}">
        <i class="bi bi-robot me-1"></i>Dashboard IA
    </a>
</li>
```

### Afficher les Prédictions sur une Page Événement

Dans `events/show.blade.php`:

```blade
@php
    $aiService = new \App\Services\TounsiVertAIService();
    $prediction = $aiService->predictEventParticipation($event);
@endphp

<div class="alert alert-info">
    <i class="bi bi-robot me-2"></i>
    <strong>Prédiction IA:</strong> 
    {{ $prediction['predicted_participants'] }} participants attendus
    (Confiance: {{ $prediction['confidence'] }}%)
</div>
```

---

## 📊 API ENDPOINTS

### Pour les Développeurs

```php
// Recommandations (JSON)
GET /member/api/ai/recommendations?limit=5

// Prédiction d'événement (JSON)
GET /admin/ai/event/{id}/predict

// Réponse exemple:
{
    "predicted_participants": 45,
    "confidence": 85,
    "factors": ["Moyenne historique: 40", "Popularité catégorie: 50"],
    "range": {
        "min": 31,
        "max": 58
    }
}
```

---

## 🧪 TESTER L'IA

### Test 1: Recommandations

```bash
# 1. Créer des participations pour un user
php artisan tinker

$user = User::find(3); // Member
$events = Event::take(3)->get();
foreach($events as $event) {
    Participation::create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => 'confirmed'
    ]);
}

# 2. Tester les recommandations
$aiService = new \App\Services\TounsiVertAIService();
$recs = $aiService->recommendEventsForUser($user, 5);
print_r($recs);
```

### Test 2: Anomalies

```bash
php artisan tinker

$org = Organization::first();
$aiService = new \App\Services\TounsiVertAIService();
$anomalies = $aiService->detectOrganizationAnomalies($org);
print_r($anomalies);
```

### Test 3: Dashboard Insights

```bash
php artisan tinker

$aiService = new \App\Services\TounsiVertAIService();
$insights = $aiService->generateAIDashboardInsights();
print_r($insights);
```

---

## 🎯 RÉSUMÉ DES URLS

| Fonctionnalité | URL | Rôle Requis |
|----------------|-----|-------------|
| Dashboard IA | `/admin/ai/dashboard` | Admin |
| Anomalies | `/admin/ai/anomalies` | Admin |
| Analyse Org | `/admin/ai/organization/{id}` | Admin |
| Recommandations | `/member/ai/recommendations` | Member |
| API Recommandations | `/member/api/ai/recommendations` | Member |
| Prédiction Event | `/admin/ai/event/{id}/predict` | Admin |

---

## 💡 CONSEILS D'UTILISATION

### Pour les Admins:
1. ✅ Consultez le dashboard IA quotidiennement
2. ✅ Surveillez les anomalies chaque semaine
3. ✅ Utilisez les prédictions pour planifier les ressources
4. ✅ Analysez les organisations avant de les vérifier

### Pour les Members:
1. ✅ Consultez vos recommandations régulièrement
2. ✅ Participez à des événements pour améliorer les recommandations
3. ✅ L'IA apprend de vos préférences au fil du temps

---

## 🚀 PROCHAINES ÉTAPES

Pour améliorer l'IA:

1. **Ajouter plus de données** - Plus d'événements et participations
2. **Affiner les algorithmes** - Ajuster les poids dans `TounsiVertAIService.php`
3. **Ajouter des notifications** - Alerter les admins des anomalies
4. **Créer des rapports** - Export PDF des analyses IA

---

## 📞 SUPPORT

Si vous avez des questions:
- 📖 Consultez `TounsiVertAIService.php` pour le code
- 🔧 Modifiez les paramètres dans le service
- 📧 Contactez le support technique

---

**🌱 Tounsi-Vert - Intelligence Artificielle au Service de l'Environnement! 🤖**

**Version:** 1.0.0  
**Date:** 2025-10-23  
**Status:** ✅ OPÉRATIONNEL
