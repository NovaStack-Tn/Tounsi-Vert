# 🧪 Guide de Test - Fonctionnalités IA et Avancées

## 🚀 Installation et Configuration

### 1. Exécuter les Migrations

```bash
cd backend
php artisan migrate
```

**Vérification:**
- ✅ Migration `add_ai_fields_to_reports_table` exécutée
- ✅ Colonnes AI ajoutées à la table `reports`

### 2. Générer les Données de Test

```bash
# Réinitialiser complètement
php artisan migrate:fresh
php artisan db:seed

# Créer organisations et événements
php artisan db:seed --class=TestDataSeeder

# Créer reports avec analyse IA
php artisan db:seed --class=ReportSeeder
```

### 3. Démarrer le Serveur

```bash
php artisan serve
```

---

## 🧪 Tests de l'Intelligence Artificielle

### Test 1: Création de Report avec Analyse IA

**Objectif:** Vérifier que l'IA analyse automatiquement le contenu

**Étapes:**
1. Se connecter comme `member@tounsivert.tn` / `password`
2. Aller sur `/organizations/1`
3. Cliquer "Report Organization"
4. Remplir le formulaire:
   ```
   Category: Spam
   Priority: (laisser vide pour test IA)
   Reason: "This is promotional spam content buy now!"
   Details: "Click here for free money! Limited offer!"
   ```
5. Soumettre

**Résultats Attendus:**
- ✅ Report créé avec succès
- ✅ Message personnalisé selon le risque
- ✅ Champs IA remplis automatiquement:
  - `ai_risk_score` > 0
  - `ai_suggested_category` = "spam"
  - `ai_confidence` > 70%
  - `ai_analysis` contient les scores

**Vérification en Base:**
```sql
SELECT id, reason, ai_risk_score, ai_suggested_category, ai_confidence, ai_auto_flagged
FROM reports
ORDER BY id DESC
LIMIT 1;
```

### Test 2: Détection de Contenu Violent

**Objectif:** Vérifier l'auto-flagging pour contenu dangereux

**Données:**
```
Category: Violence
Reason: "Threatening violence and dangerous content"
Details: "This organization posted violent threats and attack plans with weapons. Very dangerous!"
```

**Résultats Attendus:**
- ✅ `ai_risk_score` ≥ 80
- ✅ `ai_auto_flagged` = true
- ✅ `ai_suggested_category` = "violence"
- ✅ Message: "Your report has been flagged for immediate attention"

### Test 3: Détection de Fraude

**Données:**
```
Category: Fraud
Reason: "Scam and phishing attempt"
Details: "This is a fake organization running a ponzi scheme to steal money and cheat people."
```

**Résultats Attendus:**
- ✅ `ai_risk_score` ≥ 60
- ✅ Score fraude élevé dans `ai_analysis`
- ✅ Priorité suggérée: "high" ou "critical"

### Test 4: Contenu Bénin

**Données:**
```
Category: Other
Reason: "Minor issue with event details"
Details: "The event description has a small typo that should be corrected."
```

**Résultats Attendus:**
- ✅ `ai_risk_score` < 40
- ✅ `ai_auto_flagged` = false
- ✅ Priorité suggérée: "low"

---

## 📊 Tests des Analytics

### Test 5: Dashboard Analytics

**Objectif:** Vérifier l'affichage des statistiques

**Étapes:**
1. Se connecter comme `admin@tounsivert.tn` / `password`
2. Aller sur `/admin/reports`
3. Cliquer sur "Analytics"

**Vérifications:**
- ✅ Cartes de statistiques affichées (Total, Open, In Review, Resolution Rate)
- ✅ Graphique en donut pour priorités
- ✅ Graphique en barres pour catégories
- ✅ Section "Trends" avec données de la semaine/mois
- ✅ "Top Reported Organizations" avec liste
- ✅ "Resolution Statistics" avec taux
- ✅ Temps de réponse moyen calculé

**Test des Graphiques:**
- ✅ Chart.js chargé (vérifier console)
- ✅ Graphiques interactifs (hover)
- ✅ Couleurs correctes
- ✅ Données cohérentes

### Test 6: Statistiques en Temps Réel

**Objectif:** Vérifier que les stats se mettent à jour

**Étapes:**
1. Noter les statistiques actuelles
2. Créer un nouveau report
3. Rafraîchir le dashboard analytics

**Résultats Attendus:**
- ✅ Total Reports +1
- ✅ Open Reports +1
- ✅ Graphiques mis à jour
- ✅ Tendances actualisées

---

## 🔍 Tests de Recherche Avancée

### Test 7: Recherche Textuelle

**Objectif:** Rechercher dans reason et details

**Étapes:**
1. Aller sur `/admin/reports`
2. Cliquer "Advanced Search"
3. Entrer "spam" dans "Search Text"
4. Cliquer "Search"

**Résultats Attendus:**
- ✅ Seuls les reports contenant "spam" affichés
- ✅ Recherche insensible à la casse
- ✅ Recherche dans reason ET details

### Test 8: Filtres Combinés

**Objectif:** Tester plusieurs filtres simultanément

**Filtres:**
```
Search: "fraud"
Status: open
Priority: high
Category: fraud
Date From: 2025-10-01
Date To: 2025-10-31
```

**Résultats Attendus:**
- ✅ Tous les filtres appliqués
- ✅ Résultats filtrés correctement
- ✅ Pagination fonctionne
- ✅ URL contient tous les paramètres

### Test 9: Tri par Score IA

**Objectif:** Trier par risque décroissant

**Étapes:**
1. Advanced Search
2. Sort By: "AI Risk Score"
3. Sort Order: Descendant (implicite)
4. Search

**Résultats Attendus:**
- ✅ Reports triés par `ai_risk_score` DESC
- ✅ Scores les plus élevés en premier
- ✅ Ordre cohérent

### Test 10: Recherche par Date

**Objectif:** Filtrer par plage de dates

**Filtres:**
```
Date From: 2025-10-20
Date To: 2025-10-23
```

**Résultats Attendus:**
- ✅ Seuls les reports de cette période
- ✅ Dates inclusives
- ✅ Aucun report hors période

---

## 🎨 Tests d'Interface

### Test 11: Badges IA dans la Liste

**Objectif:** Vérifier l'affichage des badges IA

**Étapes:**
1. Aller sur `/admin/reports`
2. Observer les reports

**Vérifications:**
- ✅ Badge "Risk: XX" affiché si score > 0
- ✅ Couleur du badge selon niveau (low=success, high=warning, critical=danger)
- ✅ Badge "AI Flagged" si auto-flagged
- ✅ Icône robot présente

### Test 12: Alerte de Risque Élevé

**Objectif:** Vérifier l'alerte pour reports à haut risque

**Condition:** Au moins 1 report avec `ai_risk_score` ≥ 70

**Résultats Attendus:**
- ✅ Alerte rouge affichée en haut
- ✅ Texte: "AI Alert: High Risk Reports Detected"
- ✅ Nombre de reports à haut risque correct
- ✅ Lien vers liste filtrée fonctionne

### Test 13: Carte AI Flagged

**Objectif:** Vérifier la statistique AI Flagged

**Vérifications:**
- ✅ Carte "AI Flagged" affichée
- ✅ Couleur rouge foncé
- ✅ Icône robot
- ✅ Nombre correct de reports auto-flagged

### Test 14: Analyse IA dans Détails

**Objectif:** Vérifier l'affichage de l'analyse complète

**Étapes:**
1. Cliquer sur "View Details & Actions" d'un report
2. Observer la section "AI Analysis"

**Vérifications:**
- ✅ Section "AI Analysis" affichée
- ✅ Suggested Category avec badge
- ✅ Confidence en pourcentage
- ✅ Risk Level avec badge coloré
- ✅ Category Scores avec barres de progression
- ✅ Alerte si "requires_immediate_attention"

---

## 🔧 Tests Fonctionnels

### Test 15: Service ReportAnalysisService

**Objectif:** Tester directement le service

```bash
php artisan tinker
```

```php
use App\Services\ReportAnalysisService;

$service = new ReportAnalysisService();

// Test 1: Spam
$result = $service->analyzeReportContent(
    "Promotional spam buy now",
    "Click here for free money limited offer"
);
print_r($result);
// Attendu: suggested_category = "spam", risk_score > 50

// Test 2: Violence
$result = $service->analyzeReportContent(
    "Violent threats",
    "This organization posted violent attack plans with weapons"
);
print_r($result);
// Attendu: suggested_category = "violence", auto_flag = true

// Test 3: Organization Risk Profile
$org = \App\Models\Organization::first();
$profile = $service->getOrganizationRiskProfile($org);
print_r($profile);
// Attendu: risk_level, total_reports, recommendation

// Test 4: Advanced Statistics
$stats = $service->getAdvancedStatistics();
print_r($stats);
// Attendu: overview, by_priority, by_category, trends, etc.
```

### Test 16: Recherche Programmatique

```php
$service = new ReportAnalysisService();

$filters = [
    'search' => 'spam',
    'status' => 'open',
    'priority' => 'high',
    'category' => 'spam',
];

$query = $service->searchReports($filters);
$reports = $query->get();

echo "Found: " . $reports->count() . " reports\n";
// Vérifier que les filtres sont appliqués
```

### Test 17: Scopes Eloquent

```php
use App\Models\Report;

// High Risk Reports
$highRisk = Report::highRisk()->get();
echo "High Risk: " . $highRisk->count() . "\n";

// Auto-Flagged Reports
$autoFlagged = Report::autoFlagged()->get();
echo "Auto-Flagged: " . $autoFlagged->count() . "\n";

// Open High Risk
$openHighRisk = Report::open()->highRisk()->get();
echo "Open High Risk: " . $openHighRisk->count() . "\n";
```

---

## 📈 Tests de Performance

### Test 18: Temps d'Analyse IA

**Objectif:** Mesurer la performance de l'analyse

```php
$service = new ReportAnalysisService();

$start = microtime(true);
$result = $service->analyzeReportContent(
    "Test reason with multiple words",
    "Test details with more content to analyze including various patterns"
);
$end = microtime(true);

$time = ($end - $start) * 1000;
echo "Analysis time: {$time}ms\n";
// Attendu: < 100ms
```

### Test 19: Performance de Recherche

**Objectif:** Tester la vitesse de recherche

```php
$service = new ReportAnalysisService();

$start = microtime(true);
$query = $service->searchReports([
    'search' => 'test',
    'status' => 'open',
    'priority' => 'high',
]);
$reports = $query->paginate(20);
$end = microtime(true);

$time = ($end - $start) * 1000;
echo "Search time: {$time}ms\n";
// Attendu: < 500ms
```

### Test 20: Performance Analytics

**Objectif:** Mesurer le temps de génération des stats

```php
$service = new ReportAnalysisService();

$start = microtime(true);
$stats = $service->getAdvancedStatistics();
$end = microtime(true);

$time = ($end - $start) * 1000;
echo "Analytics time: {$time}ms\n";
// Attendu: < 1000ms
```

---

## ✅ Checklist de Validation

### Backend
- [ ] Migration exécutée sans erreur
- [ ] Champs IA présents dans la table
- [ ] Service ReportAnalysisService fonctionne
- [ ] Analyse automatique au submit
- [ ] Scopes Eloquent fonctionnent
- [ ] Recherche avancée retourne résultats corrects
- [ ] Analytics calculent correctement

### Frontend
- [ ] Badges IA affichés dans liste
- [ ] Alerte de risque élevé fonctionne
- [ ] Carte AI Flagged affichée
- [ ] Section AI Analysis dans détails
- [ ] Graphiques Chart.js chargent
- [ ] Recherche avancée UI fonctionne
- [ ] Dashboard analytics s'affiche

### Fonctionnalités IA
- [ ] Détection spam fonctionne
- [ ] Détection violence fonctionne
- [ ] Détection fraude fonctionne
- [ ] Auto-flagging pour contenu dangereux
- [ ] Scores de confiance cohérents
- [ ] Priorité suggérée appropriée

### Performance
- [ ] Analyse < 100ms
- [ ] Recherche < 500ms
- [ ] Analytics < 1s
- [ ] Pas de N+1 queries
- [ ] Pagination fonctionne

---

## 🐛 Problèmes Courants et Solutions

### Problème 1: Champs IA NULL

**Symptôme:** `ai_risk_score` est 0 ou NULL

**Solution:**
```bash
# Régénérer les reports avec IA
php artisan migrate:fresh
php artisan db:seed
php artisan db:seed --class=TestDataSeeder
php artisan db:seed --class=ReportSeeder
```

### Problème 2: Graphiques ne s'affichent pas

**Symptôme:** Canvas vide dans analytics

**Solution:**
- Vérifier que Chart.js est chargé (console)
- Vérifier les données JSON dans le code source
- Vider le cache du navigateur

### Problème 3: Recherche ne retourne rien

**Symptôme:** Aucun résultat malgré des reports existants

**Solution:**
- Vérifier les filtres appliqués
- Tester sans filtres
- Vérifier la requête SQL générée

### Problème 4: Erreur 500 sur analytics

**Symptôme:** Page analytics crash

**Solution:**
```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Vider le cache
php artisan cache:clear
php artisan config:clear
```

---

## 📊 Résultats Attendus

### Après Seeding Complet

**Statistiques Typiques:**
- Total Reports: 10-20
- Open: 2-5
- In Review: 2-5
- Resolved: 3-8
- Dismissed: 1-3
- AI Flagged: 1-3
- High Risk: 2-5

**Distribution Priorités:**
- Critical: 10-20%
- High: 20-30%
- Medium: 30-40%
- Low: 20-30%

**Distribution Catégories:**
- Spam: 15-25%
- Fraud: 10-20%
- Harassment: 10-15%
- Violence: 5-15%
- Autres: 40-50%

---

## 🎉 Validation Finale

Pour considérer le système comme validé:

1. ✅ Tous les tests passent
2. ✅ Aucune erreur dans les logs
3. ✅ Performance acceptable
4. ✅ UI responsive et fonctionnelle
5. ✅ Données cohérentes
6. ✅ IA analyse correctement
7. ✅ Recherche retourne résultats pertinents
8. ✅ Analytics affichent données correctes

**Le système est prêt pour la production!** 🚀

---

**Guide créé le:** 2025-10-23  
**Version:** 3.0.0  
**Status:** ✅ COMPLET
