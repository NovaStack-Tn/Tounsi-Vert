# 📋 Système de Reports Complet - Documentation

## 🎯 Vue d'ensemble

Le système de reports a été complètement refait avec **deux tables liées par jointure** pour gérer les signalements des organisations et événements, ainsi que les actions administratives prises sur ces signalements.

---

## 🗄️ Structure de la Base de Données

### Table 1: `reports` (Signalements)

Stocke tous les signalements soumis par les utilisateurs.

**Colonnes principales:**
- `id` - Identifiant unique
- `user_id` - Utilisateur qui a soumis le signalement (FK → users)
- `organization_id` - Organisation signalée (FK → organizations, nullable)
- `event_id` - Événement signalé (FK → events, nullable)
- `category` - Catégorie du signalement (ENUM)
  - spam
  - inappropriate
  - fraud
  - harassment
  - violence
  - misinformation
  - copyright
  - other
- `priority` - Niveau de priorité (ENUM)
  - low
  - medium
  - high
  - critical
- `reason` - Résumé bref (max 200 caractères)
- `details` - Détails complets (TEXT)
- `status` - Statut actuel (ENUM)
  - open
  - in_review
  - resolved
  - dismissed
- `resolved_by` - Admin qui a résolu (FK → users, nullable)
- `resolved_at` - Date de résolution (TIMESTAMP, nullable)
- `created_at`, `updated_at`

### Table 2: `report_actions` (Actions Administratives)

Stocke toutes les actions prises par les administrateurs sur les signalements.

**Colonnes principales:**
- `id` - Identifiant unique
- `report_id` - Signalement concerné (FK → reports) **← JOINTURE**
- `admin_id` - Admin qui a effectué l'action (FK → users)
- `action_type` - Type d'action (ENUM)
  - reviewed
  - investigating
  - resolved
  - dismissed
  - warning_sent
  - content_removed
  - account_suspended
- `action_note` - Note publique visible par l'utilisateur
- `internal_note` - Note interne pour les admins
- `action_taken_at` - Date/heure de l'action
- `created_at`, `updated_at`

---

## 🔗 Relations et Jointures

### Modèle Report (App\Models\Report)

```php
// Relations
public function user()           // Utilisateur qui a signalé
public function organization()   // Organisation signalée
public function event()          // Événement signalé
public function resolver()       // Admin qui a résolu
public function actions()        // TOUTES les actions (hasMany)
public function latestAction()   // Dernière action (hasOne)

// Scopes
public function scopeOpen($query)
public function scopeForOrganization($query, $organizationId)

// Accesseurs
public function getStatusBadgeAttribute()
public function getPriorityBadgeAttribute()
public function getCategoryLabelAttribute()
```

### Modèle ReportAction (App\Models\ReportAction)

```php
// Relations
public function report()  // Signalement parent
public function admin()   // Admin qui a effectué l'action

// Accesseurs
public function getActionTypeLabelAttribute()
public function getActionTypeBadgeAttribute()
```

---

## 📊 Exemple de Requête avec Jointure

### Dans le Controller (OrganizationController)

```php
// Récupérer les reports avec leurs actions (JOIN automatique via Eloquent)
$reports = $organization->reports()
    ->with(['user', 'actions.admin', 'latestAction'])
    ->where('status', '!=', 'dismissed')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

// Compter les reports par statut
$reportsCount = [
    'total' => $organization->reports()->count(),
    'open' => $organization->reports()->where('status', 'open')->count(),
    'in_review' => $organization->reports()->where('status', 'in_review')->count(),
    'resolved' => $organization->reports()->where('status', 'resolved')->count(),
];
```

### SQL Généré (simplifié)

```sql
SELECT reports.*, 
       users.first_name, users.last_name,
       report_actions.action_type, report_actions.action_note,
       admins.first_name as admin_first_name
FROM reports
LEFT JOIN users ON reports.user_id = users.id
LEFT JOIN report_actions ON reports.id = report_actions.report_id
LEFT JOIN users as admins ON report_actions.admin_id = admins.id
WHERE reports.organization_id = ?
  AND reports.status != 'dismissed'
ORDER BY reports.created_at DESC
LIMIT 5;
```

---

## 🎨 Affichage dans la Vue (organizations/show.blade.php)

### Section Reports

La vue affiche:

1. **Résumé des Reports**
   - Total des signalements
   - Nombre par statut (open, in_review, resolved)

2. **Liste des Reports Récents**
   Pour chaque report:
   - Badges de statut, catégorie et priorité
   - Raison et détails
   - **Actions administratives (données de la jointure)**
     - Type d'action
     - Admin qui l'a effectuée
     - Date et heure
     - Note publique
   - Utilisateur qui a signalé

### Code Blade

```blade
@foreach($reports as $report)
    <div class="border rounded p-3 mb-3 bg-light">
        <!-- Badges -->
        <span class="badge bg-{{ $report->statusBadge }}">{{ ucfirst($report->status) }}</span>
        <span class="badge bg-info">{{ $report->categoryLabel }}</span>
        <span class="badge bg-{{ $report->priorityBadge }}">{{ ucfirst($report->priority) }}</span>
        
        <!-- Raison et détails -->
        <p><strong>Reason:</strong> {{ $report->reason }}</p>
        <p>{{ Str::limit($report->details, 100) }}</p>

        <!-- Actions (JOINTURE) -->
        @if($report->actions->count() > 0)
            <div class="mt-2 pt-2 border-top">
                <strong>Actions Taken ({{ $report->actions->count() }}):</strong>
                @foreach($report->actions->take(2) as $action)
                    <div class="ms-3 mb-2">
                        <span class="badge bg-{{ $action->actionTypeBadge }}">
                            {{ $action->actionTypeLabel }}
                        </span>
                        <small>by {{ $action->admin->full_name }} - {{ $action->action_taken_at->diffForHumans() }}</small>
                        @if($action->action_note)
                            <small class="fst-italic">"{{ Str::limit($action->action_note, 80) }}"</small>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endforeach
```

---

## 🚀 Utilisation

### 1. Créer un Report

```php
Report::create([
    'user_id' => auth()->id(),
    'organization_id' => $organizationId,
    'category' => 'spam',
    'priority' => 'high',
    'reason' => 'Posting spam content',
    'details' => 'This organization is repeatedly posting spam...',
    'status' => 'open',
]);
```

### 2. Ajouter une Action Administrative

```php
ReportAction::create([
    'report_id' => $report->id,
    'admin_id' => auth()->id(),
    'action_type' => 'reviewed',
    'action_note' => 'Report has been reviewed by our team.',
    'internal_note' => 'Verified - spam content confirmed',
    'action_taken_at' => now(),
]);
```

### 3. Résoudre un Report

```php
$report->update([
    'status' => 'resolved',
    'resolved_by' => auth()->id(),
    'resolved_at' => now(),
]);

// Ajouter l'action de résolution
ReportAction::create([
    'report_id' => $report->id,
    'admin_id' => auth()->id(),
    'action_type' => 'resolved',
    'action_note' => 'Issue has been resolved. Content removed.',
]);
```

---

## 📁 Fichiers Modifiés/Créés

### Migrations
- ✅ `2024_01_01_000009_create_reports_table.php` (modifié)
- ✅ `2024_01_01_000010_create_report_actions_table.php` (nouveau)

### Modèles
- ✅ `app/Models/Report.php` (modifié)
- ✅ `app/Models/ReportAction.php` (nouveau)

### Controllers
- ✅ `app/Http/Controllers/OrganizationController.php` (modifié)
- ✅ `app/Http/Controllers/Member/ReportController.php` (modifié)

### Vues
- ✅ `resources/views/organizations/show.blade.php` (modifié)
- ✅ `resources/views/member/reports/create.blade.php` (modifié)

### Seeders
- ✅ `database/seeders/ReportSeeder.php` (nouveau)
- ✅ `database/seeders/TestDataSeeder.php` (nouveau)
- ✅ `database/seeders/DatabaseSeeder.php` (modifié)

---

## 🧪 Tests

### Exécuter les Migrations

```bash
php artisan migrate:fresh --seed
```

### Créer des Données de Test

```bash
php artisan db:seed --class=TestDataSeeder
php artisan db:seed --class=ReportSeeder
```

### Vérifier les Données

```bash
php test_reports.php
```

---

## 🎯 Avantages du Système

1. **Séparation des Préoccupations**
   - Table `reports` pour les signalements
   - Table `report_actions` pour l'historique des actions

2. **Traçabilité Complète**
   - Chaque action administrative est enregistrée
   - Historique complet visible

3. **Jointure Efficace**
   - Eloquent gère automatiquement les jointures
   - Performance optimisée avec `with()`

4. **Flexibilité**
   - Catégories et priorités personnalisables
   - Statuts multiples
   - Actions variées

5. **Interface Utilisateur**
   - Affichage clair des reports
   - Badges colorés pour statut/priorité
   - Historique des actions visible

---

## 📝 Notes Importantes

- Les reports peuvent être pour des **organisations** OU des **événements**
- Un report peut avoir **plusieurs actions** (relation one-to-many)
- Les actions sont triées par date (plus récente en premier)
- Seuls les reports non-dismissed sont affichés dans la vue publique
- Les notes internes ne sont visibles que par les admins

---

## 🔐 Sécurité

- Validation des données d'entrée
- Foreign keys avec cascade/set null appropriés
- Index sur les colonnes fréquemment requêtées
- Soft deletes sur les organisations (préserve les reports)

---

**Système créé le:** 2025-10-23  
**Version:** 1.0  
**Auteur:** Cascade AI Assistant
