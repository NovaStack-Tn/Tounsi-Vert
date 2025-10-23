# 🚀 Guide de Démarrage Rapide - Système de Reports

## ⚡ Installation et Test en 5 Minutes

### Étape 1: Réinitialiser la Base de Données

```bash
cd backend
php artisan migrate:fresh
php artisan db:seed
```

### Étape 2: Créer des Données de Test

```bash
php artisan db:seed --class=TestDataSeeder
php artisan db:seed --class=ReportSeeder
```

### Étape 3: Démarrer le Serveur

```bash
php artisan serve
```

### Étape 4: Se Connecter

Visitez: `http://localhost:8000/login`

**Comptes de test:**

```
Admin:
Email: admin@tounsivert.tn
Password: password

Organizer:
Email: organizer@tounsivert.tn
Password: password

Member:
Email: member@tounsivert.tn
Password: password
```

---

## 🎯 Tester les Fonctionnalités

### 1. Front-Office (Vue Publique)

**Voir les Reports sur une Organisation:**

1. Visitez: `http://localhost:8000/organizations/1`
2. Scrollez vers le bas
3. Vous verrez la section "Community Reports" (si des reports existent)
4. Affichage des:
   - Statistiques (total, open, in_review, resolved)
   - 5 reports récents avec leurs actions
   - Timeline des actions administratives

### 2. Créer un Report (En tant que Member)

1. Connectez-vous comme `member@tounsivert.tn`
2. Visitez une page d'organisation
3. Cliquez sur "Report Organization"
4. Remplissez le formulaire:
   - **Catégorie:** Spam, Inappropriate, Fraud, etc.
   - **Priorité:** Low, Medium, High, Critical
   - **Résumé:** Description brève
   - **Détails:** Description complète
5. Soumettez

### 3. Interface Admin (Gestion des Reports)

**Liste des Reports:**

1. Connectez-vous comme `admin@tounsivert.tn`
2. Visitez: `http://localhost:8000/admin/reports`
3. Vous verrez:
   - Statistiques en haut (total, open, in_review, resolved, dismissed)
   - Filtres par statut, priorité, catégorie
   - Liste paginée des reports

**Filtrer les Reports:**

- Cliquez sur les onglets: All, Open, In Review, Resolved, Dismissed
- Utilisez les selects pour filtrer par priorité ou catégorie
- Les résultats se mettent à jour automatiquement

**Voir les Détails:**

1. Cliquez sur "View Details & Actions" sur un report
2. Vous verrez:
   - Détails complets du report
   - Item signalé (organisation ou événement)
   - Informations du reporter
   - **Timeline des actions** (jointure avec report_actions)

**Ajouter une Action:**

1. Dans la page détails, panneau de droite
2. Section "Add Action"
3. Sélectionnez un type d'action:
   - Reviewed
   - Investigating
   - Resolved
   - Dismissed
   - Warning Sent
   - Content Removed
   - Account Suspended
4. Ajoutez une note publique (visible par le reporter)
5. Ajoutez une note interne (visible admins seulement)
6. Cliquez "Add Action"
7. L'action apparaît dans la timeline

**Actions Rapides:**

- **Mark as Resolved** - Résoudre rapidement
- **Dismiss Report** - Rejeter le report
- **Suspend Organization** - Suspendre l'organisation

---

## 📊 Vérifier les Données

### Voir les Reports dans la Base de Données

```bash
php artisan tinker
```

```php
// Compter les reports
Report::count();

// Voir un report avec ses actions
$report = Report::with('actions.admin')->first();
echo "Report: " . $report->reason . "\n";
echo "Actions: " . $report->actions->count() . "\n";
foreach ($report->actions as $action) {
    echo "  - " . $action->action_type . " by " . $action->admin->full_name . "\n";
}

// Voir les reports d'une organisation
$org = Organization::first();
echo "Organization: " . $org->name . "\n";
echo "Reports: " . $org->reports()->count() . "\n";
```

---

## 🔍 Points de Vérification

### ✅ Base de Données

```sql
-- Vérifier les tables
SHOW TABLES LIKE 'report%';

-- Compter les reports
SELECT COUNT(*) FROM reports;

-- Compter les actions
SELECT COUNT(*) FROM report_actions;

-- Jointure reports + actions
SELECT 
    r.id,
    r.reason,
    r.status,
    COUNT(ra.id) as actions_count
FROM reports r
LEFT JOIN report_actions ra ON r.id = ra.report_id
GROUP BY r.id;
```

### ✅ Routes

```bash
# Lister toutes les routes de reports
php artisan route:list --name=reports

# Vérifier les routes admin
php artisan route:list --name=admin.reports
```

### ✅ Modèles

```php
// Dans tinker
$report = Report::first();

// Vérifier les relations
$report->user;           // Reporter
$report->organization;   // Organisation signalée
$report->actions;        // Actions (hasMany)
$report->latestAction;   // Dernière action
$report->resolver;       // Admin qui a résolu

// Vérifier les accesseurs
$report->statusBadge;    // Couleur du badge
$report->priorityBadge;  // Couleur de priorité
$report->categoryLabel;  // Label de catégorie
```

---

## 🎨 Captures d'Écran Attendues

### Front-Office (organizations/show)

```
┌─────────────────────────────────────────────────┐
│  🚩 Community Reports                            │
├─────────────────────────────────────────────────┤
│  Total Reports: 5                                │
│  Open: 2 | In Review: 2 | Resolved: 1           │
│                                                  │
│  📋 Recent Reports                               │
│  ┌───────────────────────────────────────────┐  │
│  │ [OPEN] [SPAM] [HIGH]    2 hours ago      │  │
│  │ Reason: Posting spam content             │  │
│  │                                           │  │
│  │ 🛡️ Actions Taken (2):                    │  │
│  │   • Reviewed by Admin User               │  │
│  │   • Investigating by Admin User          │  │
│  └───────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
```

### Admin - Liste

```
┌─────────────────────────────────────────────────┐
│  📊 Statistics                                   │
│  Total: 25 | Open: 8 | In Review: 5 | ...      │
├─────────────────────────────────────────────────┤
│  🔍 Filters                                      │
│  [All] [Open] [In Review] [Resolved]           │
│  Priority: [All ▼]  Category: [All ▼]          │
├─────────────────────────────────────────────────┤
│  📋 Reports List                                 │
│  ┌───────────────────────────────────────────┐  │
│  │ 🏢 Green Tunisia Organization 1           │  │
│  │ [HIGH] [SPAM]                             │  │
│  │ Reported by: Member Demo                  │  │
│  │ Reason: Posting spam content              │  │
│  │                                           │  │
│  │ [View Details] [Resolve] [Dismiss]       │  │
│  └───────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
```

### Admin - Détails

```
┌─────────────────────────────────────────────────┐
│  📄 Report #1                                    │
│  [OPEN] [HIGH] [SPAM]                           │
├─────────────────────────────────────────────────┤
│  Reported Item: Green Tunisia Organization 1    │
│  Reported By: Member Demo                       │
│  Reason: Posting spam content repeatedly        │
│  Details: This organization is posting...       │
├─────────────────────────────────────────────────┤
│  🕐 Actions History (3)                         │
│  │                                              │
│  ● Reviewed - Admin User                       │
│    "Report has been reviewed..."               │
│  │                                              │
│  ● Investigating - Admin User                  │
│    "Currently investigating..."                │
│  │                                              │
│  ● Resolved - Admin User                       │
│    "Issue has been resolved."                  │
└─────────────────────────────────────────────────┘
```

---

## 🐛 Dépannage

### Problème: Reports ne s'affichent pas

**Solution:**
```bash
# Vérifier qu'il y a des reports
php artisan tinker
Report::count();

# Créer des reports de test
php artisan db:seed --class=ReportSeeder
```

### Problème: Erreur 404 sur /admin/reports

**Solution:**
```bash
# Vérifier les routes
php artisan route:list --name=admin.reports

# Vider le cache
php artisan route:clear
php artisan cache:clear
```

### Problème: Actions ne s'affichent pas

**Solution:**
```php
// Dans tinker
$report = Report::with('actions')->first();
$report->actions; // Doit retourner une collection

// Vérifier la relation
$action = ReportAction::first();
$action->report; // Doit retourner le report
```

### Problème: Erreur de permission

**Solution:**
```bash
# Vérifier que vous êtes admin
php artisan tinker
$user = User::where('email', 'admin@tounsivert.tn')->first();
$user->role; // Doit être 'admin'
```

---

## 📚 Documentation Complète

Pour plus de détails, consultez:

1. **`REPORTS_SYSTEM_DOCUMENTATION.md`** - Documentation technique complète
2. **`REPORTS_SUMMARY.md`** - Résumé visuel avec diagrammes
3. **`IMPLEMENTATION_COMPLETE.md`** - Guide d'implémentation
4. **`ADMIN_INTERFACE_COMPLETE.md`** - Interface admin détaillée

---

## ✅ Checklist de Vérification

Avant de considérer le système comme opérationnel:

- [ ] Migrations exécutées sans erreur
- [ ] Seeders créent des données de test
- [ ] Page `/organizations/1` affiche la section reports
- [ ] Page `/admin/reports` accessible (admin)
- [ ] Filtres fonctionnent correctement
- [ ] Page détails affiche l'historique des actions
- [ ] Ajout d'action fonctionne
- [ ] Mise à jour de statut fonctionne
- [ ] Suspension d'organisation fonctionne
- [ ] Timeline des actions s'affiche correctement

---

## 🎉 Félicitations!

Si tous les tests passent, votre système de reports est **100% opérationnel** et prêt pour la production!

**Profitez de votre nouveau système de gestion des signalements!** 🚀
