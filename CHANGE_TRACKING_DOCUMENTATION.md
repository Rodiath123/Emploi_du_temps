# Module de Suivi et Visibilité des Modifications

## Vue d'ensemble

Le **Module de Suivi des Modifications** est un système complet et robuste pour enregistrer, suivre et afficher toutes les modifications effectuées dans l'application. Il fournit une traçabilité complète avec des règles de visibilité basées sur les permissions utilisateur.

## Architecture

### 1. **Services**

#### ChangeTrackingService
Classe principale pour la gestion et l'analyse des modifications.

**Méthodes principales:**
- `getChangeHistory($model, $user, $perPage)` - Obtain l'historique d'un modèle avec filtrage de visibilité
- `getUserChanges(User $user, $modelType, $perPage)` - Obtient les modifications d'un utilisateur
- `getFieldChanges($fieldName, $perPage)` - Obtient les modifications d'un champ spécifique
- `getChangesByDateRange($startDate, $endDate, $user, $perPage)` - Modifications dans une plage de dates
- `getChangeComparison(Audit $audit)` - Comparaison détaillée des valeurs
- `getChangeSummary($model)` - Résumé des modifications d'un modèle
- `getChangeTimeline($model, $limit)` - Timeline formatée des changements
- `getMostActiveUsers($limit, $since)` - Les utilisateurs les plus actifs
- `getMostChangedModels($limit)` - Les modèles les plus modifiés

#### AuditCleanupService
Gestion du nettoyage et archivage des données d'audit.

**Méthodes:**
- `deleteOldAudits($daysOld)` - Supprimer les audits anciens
- `deleteAuditsByAction($action, $daysOld)` - Supprimer par type d'action
- `deleteAuditsByModelType($modelType, $daysOld)` - Supprimer par type de modèle
- `getCleanupStats($daysOld)` - Statistiques de nettoyage

### 2. **Modèles**

#### Audit Model
Représente une enregistrement d'audit individual.

**Attribues:**
- `user_id` - Utilisateur qui a effectué la modification
- `model_type` - Type de modèle modifié
- `model_id` - ID du modèle modifié
- `action` - Type d'action (created, updated, deleted)
- `change_type` - Type de changement (field, relationship, meta)
- `field_name` - Nom du champ modifié
- `old_value` - Valeur précédente
- `new_value` - Nouvelle valeur
- `ip_address` - Adresse IP de l'utilisateur
- `user_agent` - User Agent du navigateur
- `metadata` - Données additionnelles en JSON

**Relations:**
- `user()` - L'utilisateur qui a effectué la modification

**Scopes:**
- `forModel($modelType)` - Filtrer par type de modèle
- `forModelId($modelId)` - Filtrer par ID de modèle
- `forAction($action)` - Filtrer par type d'action
- `byUser($userId)` - Filtrer par utilisateur
- `dateRange($startDate, $endDate)` - Filtrer par plage de dates
- `recent($hours)` - Modifications récentes
- `excludeSensitive()` - Exclure les champs sensibles
- `onlyFieldChanges()` - Seulement les changements de champs

**Accesseurs:**
- `action_label` - Label lisible de l'action
- `formatted_old_value` - Valeur précédente formatée
- `formatted_new_value` - Nouvelle valeur formatée
- `change_description` - Description du changement
- `difference` - Tableau différence (from/to)

**Méthodes:**
- `isVisibleTo($user)` - Vérifier si visible pour l'utilisateur
- `isSensitiveField()` - Vérifier si c'est un champ sensible

### 3. **Trait Auditable**

Trait à ajouter aux modèles pour activer le suivi automatique.

```php
class MyModel extends Model
{
    use \App\Traits\Auditable;
}
```

**Fonctionnement:**
- Enregistre automatiquement les modifications lors de create, update, delete
- Enregistre les valeurs avant/après
- Capture l'utilisateur actuel, l'adresse IP et le User Agent
- Ignores les timestamps

**Relations du modèle:**
- `audits()` - Obtenir tous les audits du modèle
- `getAuditHistory($perPage)` - Historique paginé
- `getLastModification()` - Dernière modification

### 4. **Policies**

#### ChangeVisibilityPolicy
Gère les permissions d'accès aux données d'audit.

**Méthodes:**
- `viewAny($user)` - Voir tous les audits (admin uniquement)
- `view($user, $audit)` - Voir un audit spécifique
- `export($user)` - Exporter les logs d'audit
- `filterByUser($user)` - Filtrer par utilisateur
- `canViewField($user, $audit, $fieldName)` - Voir un champ spécifique
- `getFilterOptions($user)` - Options de filtrage disponibles

**Règles:**
- **Admin**: Accès complet à tous les audits
- **Utilisateur régulier**: Peut voir uniquement ses propres modifications et modifications de son profil
- **Champs sensibles**: Masqués pour les utilisateurs non-admins

### 5. **Contrôleur**

#### ChangeHistoryController
API endpoints pour accéder aux données d'audit.

**Routes:**

**Historique des modèles:**
```
GET /changes/model/{modelType}/{modelId}/history
GET /changes/model/{modelType}/{modelId}/summary
GET /changes/model/{modelType}/{modelId}/timeline
```

**Modifications utilisateur:**
```
GET /changes/user/{user}/changes
```

**Modifications par champ:**
```
GET /changes/field/{fieldName}
```

**Plage de dates:**
```
GET /changes/by-date-range?start_date=2026-02-01&end_date=2026-02-28
```

**Comparaison:**
```
GET /changes/comparison/{audit}
```

**Admin (statistiques):**
```
GET /changes/most-active-users
GET /changes/most-changed-models
POST /changes/export
```

### 6. **Vues Blade**

#### Index
`resources/views/changes/index.blade.php` - Liste complète avec filtres

#### History
`resources/views/changes/history.blade.php` - Historique d'un modèle spécifique

#### Comparison
`resources/views/changes/comparison.blade.php` - Comparaison détaillée d'une modification

## Utilisation

### Configuration de base

1. **Ajouter le trait Auditable aux modèles:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class User extends Model
{
    use Auditable;
}
```

2. **Exécuter les migrations:**

```bash
php artisan migrate
```

### Récupérer l'historique d'un modèle

```php
use App\Services\ChangeTrackingService;

$service = app(ChangeTrackingService::class);
$user = User::find(1);

// Obtenir l'historique paginé
$history = $service->getChangeHistory($user, auth()->user(), perPage: 15);

// Obtenir le résumé
$summary = $service->getChangeSummary($user);

// Obtenir la timeline
$timeline = $service->getChangeTimeline($user, limit: 50);
```

### Rechercher les modifications d'un utilisateur

```php
$service = app(ChangeTrackingService::class);
$user = User::find(1);

// Tous les changements de l'utilisateur
$changes = $service->getUserChanges($user);

// Changements d'un type de modèle spécifique
$changes = $service->getUserChanges($user, 'App\\Models\\Post');
```

### Filtrer par plage de dates

```php
$service = app(ChangeTrackingService::class);

$changes = $service->getChangesByDateRange(
    Carbon::parse('2026-02-01'),
    Carbon::parse('2026-02-28'),
    user: auth()->user()
);
```

### Obtenir une comparaison détaillée

```php
$service = app(ChangeTrackingService::class);
$audit = Audit::find(123);

$comparison = $service->getChangeComparison($audit);

// Résultat:
// [
//     'id' => 123,
//     'action' => 'Updated',
//     'field' => 'email',
//     'before' => 'old@example.com',
//     'after' => 'new@example.com',
//     'changed_at' => '2026-02-17 10:30:45',
//     'changed_by' => ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
//     'ip_address' => '192.168.1.1',
//     'user_agent' => '...'
// ]
```

### Analyser les utilisateurs les plus actifs

```php
$service = app(ChangeTrackingService::class);

// Top 10 dernières 24h
$users = $service->getMostActiveUsers(
    limit: 10,
    since: now()->subDay()
);
```

### Analyser les modèles les plus modifiés

```php
$service = app(ChangeTrackingService::class);

$models = $service->getMostChangedModels(limit: 10);
```

## Sécurité

### Visibilité des données

Contrôlée via `ChangeVisibilityPolicy`:
- **Administrateurs**: Accès complet
- **Utilisateurs réguliers**: Seulement leurs propres modifications
- **Champs sensibles**: Jamais visibles pour les autres utilisateurs

### Champs sensibles

Par défaut, ces champs sont masqués:
- `password`
- `api_token`
- `secret`
- `token`
- `remember_token`

### Nettoyage des données

Utiliser `AuditCleanupService` for nettoyer les anciennes données:

```php
use App\Services\AuditCleanupService;

$service = app(AuditCleanupService::class);

// Supprimer les audits plus vieux que 90 jours
$deleted = $service->deleteOldAudits(daysOld: 90);

// Obtenir les stats avant suppression
$stats = $service->getCleanupStats(daysOld: 90);
```

## API Endpoints

### Routes web
```
GET  /changes/model/{modelType}/{modelId}/history        - Change history
GET  /changes/model/{modelType}/{modelId}/summary        - Change summary
GET  /changes/model/{modelType}/{modelId}/timeline       - Change timeline
GET  /changes/user/{user}/changes                        - User changes
GET  /changes/field/{fieldName}                          - Field changes (admin)
GET  /changes/by-date-range                              - Date range changes
GET  /changes/comparison/{audit}                         - Change comparison
GET  /changes/most-active-users                          - Top active users (admin)
GET  /changes/most-changed-models                        - Most changed models (admin)
POST /changes/export                                     - Export audits (admin)
```

### Routes API
```
GET  /api/changes/models/{modelType}/{modelId}/history
GET  /api/changes/models/{modelType}/{modelId}/summary
GET  /api/changes/models/{modelType}/{modelId}/timeline
GET  /api/changes/users/{user}/changes
GET  /api/changes/fields/{fieldName}                     (admin)
GET  /api/changes/by-date-range
GET  /api/changes/comparisons/{audit}
GET  /api/changes/most-active-users                      (admin)
GET  /api/changes/most-changed-models                    (admin)
POST /api/changes/export                                 (admin)
```

## Migration

Pour mettre à niveau la table audits:

```bash
php artisan migrate
```

Cela ajoutera:
- Support pour soft deletes
- Colonne `change_type`
- Colonne `metadata` (JSON)
- Index supplémentaires pour les performances

## Exemples d'utilisation avancée

### Suivi des modifications de relations

```php
// Dans le modèle Parent
class Post extends Model
{
    use Auditable;

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

// Dans le modèle enfant
class Comment extends Model
{
    use Auditable;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

// Les changements des commentaires sont suivis indépendamment
```

### Rapport d'activité personnalisé

```php
use App\Services\ChangeTrackingService;
use Illuminate\Support\Carbon;

$service = app(ChangeTrackingService::class);

// Activité de la semaine dernière
$changes = $service->getChangesByDateRange(
    now()->subWeek()->startOfDay(),
    now()->endOfDay(),
    user: null // Tous les utilisateurs pour les admins
);

// Générer un rapport
foreach ($changes as $change) {
    echo "{$change->user->name} a {$change->action_label} ";
    echo "le champ {$change->field_name} le {$change->created_at->format('Y-m-d H:i')}";
}
```

## Performance

- Indexes sur `(model_type, model_id, created_at)` pour les requêtes de modèle
- Indexes sur `(user_id, created_at)` pour les requêtes utilisateur
- Indexes sur `field_name` et `action` pour le filtrage
- Soft deletes pour préserver les données archivées

## Dépannage

**Q: Les modifications ne sont pas enregistrées?**
- Vérifier que le modèle utilise le trait `Auditable`
- Vérifier que le middleware d'authentification est actif

**Q: Les permissions d'accès sont refusées?**
- Vérifier que `ChangeVisibilityPolicy` est enregistrée dans `AuthServiceProvider`
- Pour les admins, vérifier que `isAdmin()` retourne true

**Q: Les performances se dégradent?**
- Utiliser `AuditCleanupService` pour archiver les anciennes données
- Ajouter des indexes supplémentaires si nécessaire

## Notes

- Le suivi commence après l'utilisation du trait `Auditable`
- Les modifications antérieures ne sont pas capturées
- Les modifications en masse (bulk updates) enregistrent chaque changement individuellement
- Les métadonnées JSON contiennent le contexte complet de la modification
