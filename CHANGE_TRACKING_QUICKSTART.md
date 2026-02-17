# Module de Suivi et Visibilité des Modifications - Guide de Démarrage Rapide

## Vue d'ensemble

Le **Module de Suivi des Modifications** fournit un système complet pour enregistrer, analyser et afficher toutes les modifications effectuées dans votre application Laravel avec des règles de visibilité basées sur les permissions.

## 🚀 Installation Rapide

### 1. Ajouter le trait Auditable aux modèles à tracer

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable; // ← Ajouter cette ligne

class User extends Model
{
    use Auditable; // ← Ajouter cette ligne
}
```

### 2. Exécuter les migrations

```bash
php artisan migrate
```

### 3. Enregistrer la Policy (dans `app/Providers/AuthServiceProvider.php`)

```php
use App\Models\Audit;
use App\Policies\ChangeVisibilityPolicy;

protected $policies = [
    Audit::class => ChangeVisibilityPolicy::class,
];
```

## 📊 Utilisation Basic

### Afficher l'historique d'un modèle

```php
use App\Services\ChangeTrackingService;

$service = app(ChangeTrackingService::class);
$user = User::find(1);

// Obtenir l'historique paginé
$history = $service->getChangeHistory($user, auth()->user(), perPage: 15);

return view('changes.history', [
    'model' => $user,
    'history' => $history,
    'summary' => $service->getChangeSummary($user),
]);
```

### Accéder au historique directement depuis le modèle

```php
$user = User::find(1);

// méthodes disponibles:
$user->audits();                    // Tous les audits
$user->getAuditHistory(perPage: 20); // Historique paginé
$user->getLastModification();       // Dernière modification
```

## 🎯 Cas d'usage courants

### 1. Afficher qui a changé le profil d'un utilisateur

```php
$user = User::find(1);
$changes = $user->audits()
    ->where('model_id', 1)
    ->where('model_type', User::class)
    ->latest()
    ->get();

foreach ($changes as $change) {
    echo "{$change->user->name} a {$change->action_label} 
           le champ {$change->field_name} le {$change->created_at}";
}
```

### 2. Générer un rapport hebdomadaire

```php
$service = app(ChangeTrackingService::class);

$changes = $service->getChangesByDateRange(
    now()->startOfWeek(),
    now()->endOfWeek()
);

echo "Total modifications cette semaine: {$changes->total()}";
```

### 3. Trouver les utilisateurs les plus actifs

```php
$service = app(ChangeTrackingService::class);

$topUsers = $service->getMostActiveUsers(limit: 10, since: now()->subDay());

foreach ($topUsers as $item) {
    echo "{$item['user']->name}: {$item['changes']} modifications (dernières 24h)";
}
```

### 4. Archiver les anciennes données d'audit

```php
$service = app(AuditCleanupService::class);

// Afficher les stats
$stats = $service->getCleanupStats(daysOld: 90);
echo "Audits à supprimer: {$stats['audits_to_delete']}";

// Supprimer
$deleted = $service->deleteOldAudits(daysOld: 90);
echo "Supprimés: {$deleted}";
```

## 🛣️ Routes disponibles

### Routes Web
```
GET    /changes/model/{modelType}/{modelId}/history      Historique d'un modèle
GET    /changes/model/{modelType}/{modelId}/summary      Résumé de modification
GET    /changes/model/{modelType}/{modelId}/timeline     Timeline formatée
GET    /changes/user/{user}/changes                      Changements d'un utilisateur
GET    /changes/field/{fieldName}                        Changements d'un champ (admin)
GET    /changes/by-date-range                            Plage de dates
GET    /changes/comparison/{audit}                       Comparaison détaillée
GET    /changes/most-active-users                        Utilisateurs actifs (admin)
GET    /changes/most-changed-models                      Modèles changés (admin)
POST   /changes/export                                   Exporter (admin)
```

### Routes API
```
GET    /api/changes/models/{modelType}/{modelId}/history
GET    /api/changes/models/{modelType}/{modelId}/summary
GET    /api/changes/models/{modelType}/{modelId}/timeline
GET    /api/changes/users/{user}/changes
GET    /api/changes/fields/{fieldName}                   (admin)
GET    /api/changes/by-date-range
GET    /api/changes/comparisons/{audit}
GET    /api/changes/most-active-users                    (admin)
GET    /api/changes/most-changed-models                 (admin)
POST   /api/changes/export                              (admin)
```

## 🔒 Sécurité & Permissions

### Règles de visibilité

- **Administrateurs**: Accès complet à tous les audits
- **Utilisateurs réguliers**: Peuvent voir seulement leurs propres modifications et modifications de leur profil
- **Champs sensibles**: Masqués automatiquement pour les utilisateurs non-admins

### Champs sensibles masqués par défaut

- `password`
- `api_token`
- `secret`
- `token`
- `remember_token`

## 📚 Structure des données

### Modèle Audit

```php
$audit->id                    // ID unique
$audit->user_id               // Utilisateur qui a fait la modification
$audit->model_type            // Type de modèle modifié
$audit->model_id              // ID du modèle modifié
$audit->action                // (created, updated, deleted)
$audit->field_name            // Champ modifié
$audit->old_value             // Valeur précédente
$audit->new_value             // Nouvelle valeur
$audit->ip_address            // IP de l'utilisateur
$audit->user_agent            // User Agent
$audit->created_at            // Timestamp
```

### Accesseurs disponibles

```php
$audit->action_label          // Label lisible ("Créé", "Modifié", etc.)
$audit->formatted_old_value   // Valeur précédente décodée
$audit->formatted_new_value   // Nouvelle valeur décodée
$audit->change_description    // Description du changement
$audit->isSensitiveField()    // bool
$audit->difference            // ['field' => '...', 'from' => '...', 'to' => '...']
```

## 🔍 Requêtes avancées

### Scopes disponibles

```php
Audit::forModel('App\\Models\\User')        // Filtrer par type de modèle
Audit::forModelId(5)                       // Filtrer par ID de modèle
Audit::forAction('updated')                // Filtrer par action
Audit::byUser(1)                           // Filtrer par utilisateur
Audit::dateRange($start, $end)             // Plage de dates
Audit::recent(hours: 24)                   // Récent (24h par défaut)
Audit::excludeSensitive()                  // Exclure champs sensibles
Audit::onlyFieldChanges()                  // Seulement changements de champs
```

### Exemples de requêtes

```php
// Modifications du profil d'un utilisateur dans les 7 derniers jours
Audit::forModel('App\\Models\\User')
    ->forModelId(1)
    ->recent(hours: 7*24)
    ->get();

// Toutes les modifications de mot de passe (admin)
Audit::where('field_name', 'password')->get();

// Modifications non-sensibles d'un utilisateur
Audit::byUser(1)->excludeSensitive()->get();

// Toutes les suppressions de la semaine dernière
Audit::forAction('deleted')
    ->whereBetween('created_at', [
        now()->subWeek()->startOfDay(),
        now()->endOfDay()
    ])
    ->get();
```

## 📈 Service ChangeTrackingService

### Méthodes principales

```php
$service = app(ChangeTrackingService::class);

// Historique d'un modèle
$service->getChangeHistory($model, $user, $perPage);

// Modifications d'un utilisateur
$service->getUserChanges($user, $modelType, $perPage);

// Modifications d'un champ
$service->getFieldChanges($fieldName, $perPage);

// Plage de dates
$service->getChangesByDateRange($startDate, $endDate, $user, $perPage);

// Comparaison détaillée
$service->getChangeComparison($audit);

// Résumé des modifications
$service->getChangeSummary($model);

// Timeline formatée
$service->getChangeTimeline($model, $limit);

// Utilisateurs actifs
$service->getMostActiveUsers($limit, $since);

// Modèles changés
$service->getMostChangedModels($limit);
```

## 🧹 Nettoyage (AuditCleanupService)

```php
$service = app(AuditCleanupService::class);

// Statistiques
$stats = $service->getCleanupStats(daysOld: 90);

// Supprimer les audits anciens
$service->deleteOldAudits(daysOld: 90);

// Supprimer par action
$service->deleteAuditsByAction('created', daysOld: 180);

// Supprimer par type de modèle
$service->deleteAuditsByModelType('App\\Models\\TempData', daysOld: 30);
```

## 🎨 Vues Blade fournies

- `resources/views/changes/index.blade.php` - Liste des modifications
- `resources/views/changes/history.blade.php` - Historique d'un modèle
- `resources/views/changes/comparison.blade.php` - Comparaison détaillée

## 📄 Documentation complète

Voir [CHANGE_TRACKING_DOCUMENTATION.md](CHANGE_TRACKING_DOCUMENTATION.md) pour la documentation détaillée.

## 💡 Exemples avancés

Voir [CHANGE_TRACKING_EXAMPLES.php](CHANGE_TRACKING_EXAMPLES.php) pour 20 exemples d'utilisation.

## ⚡ Performance

- Indexes optimisés pour les requêtes courantes
- Support des soft deletes pour archivage
- Pagination par défaut sur les résultats
- Exclusion automatique des champs sensibles

## ❓ FAQ

**Q: Comment puis-je voir mon historique de modifications?**
- Visitez `/changes/user/{id}/changes` ou utilisez l'API

**Q: Qui peut voir les modifications d'un autre utilisateur?**
- Seulement les administrateurs. Les utilisateurs réguliers ne voient que les leurs.

**Q: Que se passe-t-il si je supprime un utilisateur?**
- Les modifications associées sont conservées via soft delete pour la conformité

**Q: Comment puis-je nettoyer les anciennes données?**
- Utilisez `AuditCleanupService::deleteOldAudits()` dans une commande planifiée

## 🚀 Commandes Artisan utiles

```bash
# Nettoyer les audits (créer une commande personnalisée)
php artisan make:command CleanupAudits

# Voir la table audits
php artisan tinker
>>> App\Models\Audit::latest()->limit(10)->get()

# Exporter les données
# Utiliser le endpoint /changes/export
```

## 📞 Support

Pour les problèmes ou questions, consultez:
1. [Documentation complète](CHANGE_TRACKING_DOCUMENTATION.md)
2. [Exemples détaillés](CHANGE_TRACKING_EXAMPLES.php)
3. Vérifiez que le trait `Auditable` est ajouté au modèle
4. Vérifiez que les migrations sont exécutées

---

Créé pour fournir une traçabilité complète et une conformité audit dans les applications Laravel.
