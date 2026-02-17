# 📋 Référence Rapide - Audit Module

## 🚀 Démarrer en 3 Étapes

### 1️⃣ Migration (1 minute)
```bash
php artisan migrate
```

### 2️⃣ Ajouter le Trait (5 minutes)
```php
// app/Models/Schedule.php
use App\Traits\Auditable;

class Schedule extends Model
{
    use Auditable;
    protected $fillable = ['name', 'description'];
}
```

### 3️⃣ Tester (2 minutes)
```bash
# Web
http://localhost:8000/audits

# Terminal
php artisan audit:view

# Tinker
php artisan tinker
> $user = \App\Models\User::first();
> $user->update(['name' => 'Test']);
> \App\Models\Audit::latest()->first();
```

---

## 🎯 Commandes Essentielles

| Commande | Action |
|----------|--------|
| `php artisan migrate` | Créer la table |
| `php artisan audit:view` | Voir les audits |
| `php artisan audit:view --action=updated` | Voir les mises à jour |
| `php artisan audit:view --user-id=1` | Voir les changements d'un user |
| `php artisan audit:cleanup --days=90` | Supprimer les vieux audits |
| `php artisan audit:cleanup --preview` | Voir ce qui sera supprimé |

---

## 🔗 Routes Web

```
GET /audits              ← Admin: tous les audits
GET /audits/show        ← Historique d'un élément
GET /audits/export      ← Télécharger CSV
```

---

## 📝 Utilisation dans le Code

### Voir l'Historique
```php
$model = Schedule::find(1);
$history = $model->getAuditHistory();      // Paginated
$last = $model->getLastModification();     // Dernier changement
$audits = $model->audits()->get();         // Tous les audits
```

### Requêtes Personnalisées
```php
use App\Models\Audit;

// Par modèle
Audit::forModel('App\Models\Schedule')->get();

// Par modèle spécifique
Audit::forModelId(1)->get();

// Par action
Audit::forAction('updated')->get();

// Par utilisateur
Audit::byUser(1)->get();

// Par date
Audit::dateRange($start, $end)->get();
```

### Afficher dans les Vues
```blade
@foreach($model->getAuditHistory() as $change)
    <p>{{ $change->user->name }} 
       a modifié {{ $change->field_name }}
       de {{ $change->old_value }} à {{ $change->new_value }}
       le {{ $change->created_at->format('Y-m-d H:i') }}</p>
@endforeach
```

---

## 🔐 Permissions

```php
// Dans contrôleur
$this->authorize('viewAny', Audit::class);  // Admin only
$this->authorize('view', $audit);           // Si autorisé

// Dans blade
@can('viewAny', App\Models\Audit::class)
    <a href="/audits">Voir les audits</a>
@endcan
```

---

## 📊 Données Enregistrées

```
id              - ID unique de l'audit
user_id         - Qui a fait le changement
model_type      - Type du modèle (ex: App\Models\Schedule)
model_id        - ID du modèle
action          - created / updated / deleted
field_name      - Nom du champ changé (pour updated)
old_value       - Valeur avant
new_value       - Valeur après
ip_address      - Adresse IP
user_agent      - Navigateur/Client
created_at      - Quand ça s'est passé
```

---

## 🎨 Interface Web

**Liste (`/audits`):**
- Filtrer par type de modèle
- Filtrer par action
- Rechercher dans les valeurs
- Voir table avec tous les détails
- Exporter en CSV

**Historique (`/audits/show`):**
- Timeline verticale
- Voir ancien vs nouveau
- Info utilisateur, IP, date
- Pagination

---

## 🛠️ Configuration Utilisateur

### Modifier la Méthode isAdmin()
```php
// app/Models/User.php
public function isAdmin(): bool
{
    // Option 1: Colonne 'role'
    return $this->role === 'admin';
    
    // Option 2: Colonne booléenne
    return $this->is_admin === true;
    
    // Option 3: Vérification permission
    return $this->hasPermission('view_audits');
}
```

---

## 🧹 Nettoyage Automatique

### Une Fois
```bash
php artisan audit:cleanup --days=90
```

### Planifié (dans Kernel.php)
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('audit:cleanup', ['--days' => 90])
        ->monthly();  // Chaque mois
}
```

### Statistiques
```php
use App\Services\AuditCleanupService;

$service = new AuditCleanupService();
$stats = $service->getCleanupStats(90);
// ['total_audits' => X, 'audits_to_delete' => Y, 'by_action' => [...]]
```

---

## 🧪 Tests

```bash
# Lancer tous les tests d'audit
php artisan test --filter=AuditTest

# Ou spécifique
php artisan test tests/Feature/AuditTest.php
```

---

## 📚 Documentation Complète

| Fichier | Pour |
|---------|------|
| [AUDIT_DOCUMENTATION.md](AUDIT_DOCUMENTATION.md) | Guide complet en anglais |
| [README_AUDIT_FR.md](README_AUDIT_FR.md) | Guide complet en français |
| [QUICK_START_AUDIT.php](QUICK_START_AUDIT.php) | Exemples de code |
| [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md) | Checklist d'installation |

---

## ❌ Problèmes Courants

**Q: Les audits ne s'enregistrent pas**
```bash
# Vérifier migration
php artisan migrate:status

# Vérifier table
php artisan tinker
> DB::table('audits')->count()

# Vérifier trait ajouté au model
# Vérifier $fillable défini
```

**Q: Pas d'accès à /audits**
```bash
# Vérifier auth
> auth()->check()

# Vérifier admin
> auth()->user()->isAdmin()

# Vérifier routes
php artisan route:list | grep audit
```

**Q: Exports vides**
- Vérifier permissions (admin seulement)
- Vérifier données existent

---

## 💡 Astuces

1. **Pour voir les changes rapidement:** `php artisan audit:view`
2. **Pour filtrer:** `php artisan audit:view --action=updated`
3. **Pour nettoyer:** `php artisan audit:cleanup --preview` (d'abord voir)
4. **Pour tester:** Tinker est parfait
5. **Pour déboguer:** Vérifier Users.php isAdmin()

---

## 📞 Aide Rapide

```bash
# Besoin de documentation?
# Lire AUDIT_DOCUMENTATION.md

# Besoin d'exemples?
# Lire QUICK_START_AUDIT.php

# Besoin d'installer?
# Lire SETUP_CHECKLIST.md

# Pour tester
php artisan test --filter=AuditTest

# Pour voir le code
# app/Models/Audit.php
# app/Traits/Auditable.php
# app/Http/Controllers/AuditController.php
```

---

## ✅ Checklist Avant Production

- [ ] Migration lancée: `php artisan migrate`
- [ ] Trait ajouté aux modèles clés
- [ ] isAdmin() correctement configuré
- [ ] Tests passent: `php artisan test`
- [ ] `/audits` accessible
- [ ] Permissions testées
- [ ] Export CSV fonctionne
- [ ] Nettoyage planifié (si souhaité)

---

## 🎓 Concepts Clés

**Auditable Trait** = Auto-enregistrement des changes
**AuditController** = Affichage des audits
**AuditPolicy** = Qui peut voir quoi
**AuditCleanupService** = Gestion de l'espace disque

---

**Dernière mise à jour:** 17 février 2026
**Version:** 1.0
**Statut:** ✅ Production-Ready

---

*Besoin d'aide? Voir la documentation complète.*
