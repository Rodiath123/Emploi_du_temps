<?php

/**
 * EXEMPLE D'INTEGRATION DU MODULE DE SUIVI DES MODIFICATIONS
 * 
 * Ce fichier démontre comment intégrer et utiliser le module de suivi
 * des modifications dans votre application Laravel.
 */

namespace App\Examples;

use App\Models\User;
use App\Models\Audit;
use App\Services\ChangeTrackingService;
use App\Services\AuditCleanupService;
use Illuminate\Support\Carbon;

class ChangeTrackingExample
{
    /**
     * Exemple 1: Activer le suivi pour un modèle
     */
    public static function example1_enableTracking(): void
    {
        // Dans votre modèle User (ou tout autre modèle):
        // 
        // class User extends Model
        // {
        //     use \App\Traits\Auditable;
        // }
        //
        // C'est tout! Les modifications sont maintenant tracées automatiquement.
    }

    /**
     * Exemple 2: Récupérer l'historique d'un modèle
     */
    public static function example2_getModelHistory(): void
    {
        $service = app(ChangeTrackingService::class);
        $user = User::find(1);

        // Récupérer l'historique paginé (15 éléments par page)
        $history = $service->getChangeHistory($user, auth()->user(), 15);

        // Utiliser dans une vue
        return view('changes.history', [
            'model' => $user,
            'history' => $history,
        ]);
    }

    /**
     * Exemple 3: Obtenir un résumé des modifications
     */
    public static function example3_getChangeSummary(): void
    {
        $service = app(ChangeTrackingService::class);
        $user = User::find(1);

        $summary = $service->getChangeSummary($user);

        // Résultat:
        // [
        //     'model_type' => 'App\\Models\\User',
        //     'model_id' => 1,
        //     'total_changes' => 45,
        //     'created_at' => '2026-02-01 10:30:00',
        //     'last_modified' => '2026-02-17 15:45:00',
        //     'created_by' => 'John Doe',
        //     'modified_by_users' => ['John Doe', 'Jane Smith'],
        //     'changes_by_action' => [
        //         'created' => 1,
        //         'updated' => 42,
        //         'deleted' => 0,
        //     ],
        // ]

        echo "Total modifications: " . $summary['total_changes'];
        echo "Dernière modification: " . $summary['last_modified'];
    }

    /**
     * Exemple 4: Obtenir la timeline des changements
     */
    public static function example4_getChangeTimeline(): void
    {
        $service = app(ChangeTrackingService::class);
        $user = User::find(1);

        $timeline = $service->getChangeTimeline($user, limit: 50);

        // Chaque événement contient:
        // [
        //     'timestamp' => '2026-02-17T15:45:00Z',
        //     'action' => 'Updated',
        //     'field' => 'email',
        //     'old_value' => 'old@example.com',
        //     'new_value' => 'new@example.com',
        //     'user' => 'John Doe',
        // ]

        foreach ($timeline as $event) {
            echo "{$event['user']} a modifié {$event['field']} ({$event['timestamp']})";
        }
    }

    /**
     * Exemple 5: Obtenir les modifications d'un utilisateur
     */
    public static function example5_getUserChanges(): void
    {
        $service = app(ChangeTrackingService::class);
        $user = User::find(1);

        // Toutes les modifications de l'utilisateur #1
        $changes = $service->getUserChanges($user);

        // Modifications d'un type de modèle spécifique
        $userChanges = $service->getUserChanges($user, 'App\\Models\\User');

        foreach ($changes as $change) {
            echo "{$change->user->name} a {$change->action_label} le {$change->created_at}";
        }
    }

    /**
     * Exemple 6: Filtrer par plage de dates
     */
    public static function example6_filterByDateRange(): void
    {
        $service = app(ChangeTrackingService::class);

        $startDate = Carbon::parse('2026-02-01')->startOfDay();
        $endDate = Carbon::parse('2026-02-28')->endOfDay();

        // Modifications de février
        $changes = $service->getChangesByDateRange($startDate, $endDate);

        // Modifications d'un utilisateur spécifique
        $userChanges = $service->getChangesByDateRange(
            $startDate,
            $endDate,
            user: auth()->user()
        );

        dd($changes->items());
    }

    /**
     * Exemple 7: Obtenir une comparaison détaillée
     */
    public static function example7_getChangeComparison(): void
    {
        $service = app(ChangeTrackingService::class);
        $audit = Audit::find(123);

        $comparison = $service->getChangeComparison($audit);

        // Résultat complet avec contexte de traçabilité
        // [
        //     'id' => 123,
        //     'action' => 'Updated',
        //     'field' => 'email',
        //     'before' => 'old@example.com',
        //     'after' => 'new@example.com',
        //     'changed_at' => '2026-02-17 10:30:45',
        //     'changed_by' => [
        //         'id' => 1,
        //         'name' => 'John Doe',
        //         'email' => 'john@example.com',
        //     ],
        //     'ip_address' => '192.168.1.100',
        //     'user_agent' => 'Mozilla/5.0...',
        // ]

        echo "Valeur précédente: {$comparison['before']}";
        echo "Nouvelle valeur: {$comparison['after']}";
        echo "Modifié par: {$comparison['changed_by']['name']}";
    }

    /**
     * Exemple 8: Obtenir les modifications par champ
     */
    public static function example8_getFieldChanges(): void
    {
        $service = app(ChangeTrackingService::class);

        // Toutes les modifications du champ "email"
        $emailChanges = $service->getFieldChanges('email');

        // Afficher qui a changé quoi
        foreach ($emailChanges as $change) {
            echo "{$change->user->name} a changé le email du {$change->model_type}";
            echo " de '{$change->old_value}' à '{$change->new_value}'";
        }
    }

    /**
     * Exemple 9: Analyser l'activité des utilisateurs
     */
    public static function example9_analyzeUserActivity(): void
    {
        $service = app(ChangeTrackingService::class);

        // Top 10 utilisateurs les plus actifs (dernières 24h)
        $active = $service->getMostActiveUsers(
            limit: 10,
            since: now()->subDay()
        );

        foreach ($active as $item) {
            echo "{$item['user']->name}: {$item['changes']} modifications";
        }
    }

    /**
     * Exemple 10: Analyser les modèles les plus modifiés
     */
    public static function example10_analyzeMostChangedModels(): void
    {
        $service = app(ChangeTrackingService::class);

        // Top 10 modèles avec le plus de modifications
        $mostChanged = $service->getMostChangedModels(limit: 10);

        foreach ($mostChanged as $item) {
            echo "{$item['model_type']} #{$item['model_id']}: {$item['changes']} changes";
        }
    }

    /**
     * Exemple 11: Nettoyage des anciennes données d'audit
     */
    public static function example11_cleanupOldAudits(): void
    {
        $service = app(AuditCleanupService::class);

        // Afficher les statistiques avant suppression
        $stats = $service->getCleanupStats(daysOld: 90);

        echo "Total audits: {$stats['total_audits']}";
        echo "À supprimer: {$stats['audits_to_delete']}";
        echo "Date limite: {$stats['cutoff_date']}";

        // Supprimer les audits plus vieux que 90 jours
        $deleted = $service->deleteOldAudits(daysOld: 90);
        echo "Supprimés: {$deleted}";

        // Supprimer seulement les audits de création plus vieux que 180 jours
        $deleted = $service->deleteAuditsByAction('created', daysOld: 180);

        // Supprimer les audits d'un type de modèle spécifique
        $deleted = $service->deleteAuditsByModelType('App\\Models\\TempData', daysOld: 30);
    }

    /**
     * Exemple 12: Utiliser dans un contrôleur
     */
    public static function example12_controllerUsage(): void
    {
        // Dans votre contrôleur:
        // 
        // public function showHistory(User $user)
        // {
        //     $service = app(ChangeTrackingService::class);
        //    
        //     $history = $service->getChangeHistory(
        //         $user,
        //         auth()->user(),
        //         perPage: 15
        //     );
        //    
        //     $summary = $service->getChangeSummary($user);
        //    
        //     return view('user.history', [
        //         'user' => $user,
        //         'history' => $history,
        //         'summary' => $summary,
        //     ]);
        // }
    }

    /**
     * Exemple 13: Utiliser dans une commande Artisan
     */
    public static function example13_artisanCommand(): void
    {
        // php artisan tinker
        // > $service = app(\App\Services\ChangeTrackingService::class);
        // > $user = \App\Models\User::find(1);
        // > $service->getChangeHistory($user, \Auth::user())->items();
    }

    /**
     * Exemple 14: Reporter les modifications sensibles
     */
    public static function example14_sensitiveFIeldChanges(): void
    {
        // Obtenir seulement les changements de champs non-sensibles
        $audits = Audit::excludeSensitive()->get();

        // Obtenir les changements sensibles (pour admin)
        $sensitive = Audit::where('field_name', 'password')->get();

        // Les champs sensibles par défaut:
        // - password
        // - api_token
        // - secret
        // - token
        // - remember_token
    }

    /**
     * Exemple 15: Filtrer par action
     */
    public static function example15_filterByAction(): void
    {
        // Seulement les créations
        $created = Audit::forAction('created')->get();

        // Seulement les modifications
        $updated = Audit::forAction('updated')->get();

        // Seulement les suppessions
        $deleted = Audit::forAction('deleted')->get();
    }

    /**
     * Exemple 16: Modifications récentes
     */
    public static function example16_recentChanges(): void
    {
        // Modifications des 24 dernières heures
        $changes = Audit::recent(hours: 24)->get();

        // Modifications de la dernière heure
        $changes = Audit::recent(hours: 1)->get();
    }

    /**
     * Exemple 17: Utiliser les scopes combinés
     */
    public static function example17_combineScopes(): void
    {
        // Modifications d'un modèle spécifique dans les 24 dernières heures
        $changes = Audit::forModel('App\\Models\\User')
            ->forModelId(5)
            ->recent(hours: 24)
            ->get();

        // Modifications d'un utilisateur, excluant les champs sensibles
        $changes = Audit::byUser(1)
            ->excludeSensitive()
            ->latest()
            ->paginate(15);
    }

    /**
     * Exemple 18: Accesseurs du modèle Audit
     */
    public static function example18_auditAccessors(): void
    {
        $audit = Audit::find(1);

        // Accesseurs disponibles:
        echo $audit->action_label;              // Label lisible (ex: "Updated")
        echo $audit->formatted_old_value;       // Valeur formatée
        echo $audit->formatted_new_value;       // Nouvelle valeur formatée
        echo $audit->change_description;       // Description en une phrase
        echo $audit->isSensitiveField();       // Bool: est-ce un champ sensible?

        // Obtenir la différence
        $diff = $audit->difference; // ['field' => 'email', 'from' => 'old@.com', 'to' => 'new@.com']
    }

    /**
     * Exemple 19: Accès via l'historique du modèle
     */
    public static function example19_modelAuditAccess(): void
    {
        $user = User::find(1);

        // Récupérer l'historique d'audit paginé du modèle
        $history = $user->getAuditHistory(perPage: 20);

        // Obtenir la dernière modification
        $lastChange = $user->getLastModification();

        // Accéder à tous les audits
        foreach ($user->audits as $audit) {
            echo $audit->change_description;
        }
    }

    /**
     * Exemple 20: Intégration dans des rapports
     */
    public static function example20_reportingIntegration(): void
    {
        $service = app(ChangeTrackingService::class);

        // Rapport d'activité hebdomadaire
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $changes = $service->getChangesByDateRange($weekStart, $weekEnd);

        $report = [
            'week' => $weekStart->format('Y-m-d'),
            'total_changes' => $changes->total(),
            'by_action' => [
                'created' => $changes->whereIn('action', ['created'])->count(),
                'updated' => $changes->whereIn('action', ['updated'])->count(),
                'deleted' => $changes->whereIn('action', ['deleted'])->count(),
            ],
            'by_user' => $changes->groupBy('user_id')->count(),
        ];

        // Sauvegarder ou afficher le rapport
        dd($report);
    }
}

/**
 * NOTES D'IMPLEMENTATION:
 * 
 * 1. Le trait Auditable doit être ajouté aux modèles avant de pouvoir tracer les modifications
 * 
 * 2. Les migrations doivent être exécutées:
 *    php artisan migrate
 * 
 * 3. Les politiques doivent être enregistrées dans app/Providers/AuthServiceProvider.php:
 *    protected $policies = [
 *        Audit::class => ChangeVisibilityPolicy::class,
 *    ];
 * 
 * 4. Les routes sont automatiquement disponibles:
 *    - /changes/... (routes web)
 *    - /api/changes/... (routes API)
 * 
 * 5. Les permissions sont gérées via la Policy
 *    - Les admins voient tout
 *    - Les utilisateurs voient seulement leurs propres changements
 * 
 * 6. Les champs sensibles (password, tokens, etc.) sont masqués automatiquement
 */
