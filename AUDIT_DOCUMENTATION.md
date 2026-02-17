# Module de Suivi des Modifications - Documentation

## Overview / Aperçu

This change tracking module provides comprehensive audit logging for your Laravel application. It records all modifications to your models with automatic tracking of:

- Who made the change (user)
- What changed (field name, old/new values)
- When it happened (timestamp)
- Where it came from (IP address)
- Access control based on user roles

---

## Installation / Installation

### 1. Run Migration

```bash
php artisan migrate
```

This creates the `audits` table to store all change records.

### 2. Add Auditable Trait to Models

Apply the `Auditable` trait to any model you want to track:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Schedule extends Model
{
    use Auditable;
    
    protected $fillable = ['name', 'description', 'status'];
}
```

### 3. Update User Model

Add the `isAdmin()` method to your User model:

```php
public function isAdmin()
{
    return $this->role === 'admin'; // Adjust based on your user role system
}
```

### 4. Register Policy in AuthServiceProvider

```php
protected $policies = [
    Audit::class => AuditPolicy::class,
];
```

---

## Usage / Utilisation

### 1. Automatic Tracking

Once the `Auditable` trait is added, all CRUD operations are automatically tracked:

```php
// Create - automatically logged
$schedule = Schedule::create(['name' => 'New Schedule']);

// Update - field changes are logged
$schedule->update(['name' => 'Updated Name']);

// Delete - deletion is logged
$schedule->delete();
```

### 2. View Audit History

#### In Your Application

```
http://yourapp.com/audits
```

**Features:**
- Filter by model type
- Filter by action (created, updated, deleted)
- Search field names and values
- Export to CSV
- View detailed change history per model

#### Via Command Line

```bash
# View recent audits
php artisan audit:view

# Filter by model type
php artisan audit:view --model-type="App\Models\Schedule"

# Filter by action
php artisan audit:view --action=updated

# Filter by user
php artisan audit:view --user-id=1

# Limit results
php artisan audit:view --limit=50
```

### 3. In Your Code

```php
use App\Models\Audit;

// Get audit history for a model
$schedule = Schedule::find(1);
$history = $schedule->getAuditHistory(15); // Paginated, 15 per page

// Get last modification details
$lastChange = $schedule->getLastModification();
echo $lastChange->user->name; // Who made the last change
echo $lastChange->created_at; // When

// Query audits directly
$audits = Audit::forModel('App\Models\Schedule')
    ->forModelId(1)
    ->forAction('updated')
    ->latest()
    ->get();
```

---

## Access Control / Contrôle d'Accès

### Admin Access
- Can view ALL audit records
- Can export audit logs
- Can delete old records via cleanup command

### User Access
- Can only view their own changes
- Cannot delete or modify audits (maintains data integrity)

### Check Authorization

```php
// In your controller
$this->authorize('viewAny', Audit::class); // Admin only
$this->authorize('view', $audit); // Specific audit visibility
```

---

## Cleanup Old Records / Nettoyage des Anciens Enregistrements

### Automatic Cleanup

```bash
# Delete audits older than 90 days
php artisan audit:cleanup

# Delete audits older than 180 days
php artisan audit:cleanup --days=180

# Get cleanup statistics without deleting
php artisan audit:cleanup --preview
```

### Schedule Cleanup (in Kernel.php)

```php
protected function schedule(Schedule $schedule)
{
    // Clean up audits older than 90 days, monthly
    $schedule->command('audit:cleanup', ['--days' => 90])
        ->monthly();
}
```

### Programmatically

```php
use App\Services\AuditCleanupService;

$service = new AuditCleanupService();

// Delete old audits
$deleted = $service->deleteOldAudits(90); // days

// Get cleanup statistics
$stats = $service->getCleanupStats(90);
// ['total_audits' => X, 'audits_to_delete' => Y, 'by_action' => [...]]
```

---

## Configuration / Configuration

### Fields Tracked

By default, all fields in the model's `$fillable` array are tracked. To exclude specific fields:

```php
class Schedule extends Model
{
    use Auditable;
    
    protected $fillable = ['name', 'description', 'status'];
    
    // Optionally exclude fields from tracking
    protected $auditExclude = ['internal_field'];
}
```

### Database Retention

Adjust the cleanup command based on your compliance needs:
- **90 days**: Standard compliance
- **365 days**: Long-term tracking
- **Never delete**: Set in your policy (not recommended)

---

## Features / Caractéristiques

✅ **Automatic Tracking**
- Create, update, delete operations logged automatically

✅ **User Attribution**
- Tracks who made each change

✅ **Detailed Changes**
- Records old and new values for each field

✅ **IP & User Agent**
- Security and debugging information

✅ **Fast Queries**
- Indexed on model_type, model_id, user_id, created_at

✅ **Export to CSV**
- Download audit logs for reporting

✅ **Role-Based Access**
- Different visibility for admins vs. users

✅ **Cleanup Tools**
- Manage database size with retention policies

✅ **CLI Commands**
- View and manage audits from command line

---

## API Reference / Référence API

### Audit Model Methods

```php
$audit = Audit::first();

// Relations
$audit->user(); // Get the user who made the change

// Scopes
Audit::forModel('App\Models\Schedule')->get();
Audit::forModelId(1)->get();
Audit::forAction('updated')->get();
Audit::byUser(1)->get();
Audit::dateRange($start, $end)->get();

// Attributes
$audit->action_label; // Human-readable action name
$audit->isVisibleTo($user); // Check visibility for user
```

### Auditable Trait Methods

```php
$model = Schedule::find(1);

// Get paginated history
$history = $model->getAuditHistory(15);

// Get last modification
$lastChange = $model->getLastModification();

// Get all audits for model
$audits = $model->audits()->get();
```

---

## Examples / Exemples

### Example 1: View Changes to a Schedule

```php
// In a controller
$schedule = Schedule::findOrFail($id);
$changes = $schedule->getAuditHistory();

return view('schedule.history', ['changes' => $changes]);
```

### Example 2: Show Who Changed What

```blade
@foreach($changes as $change)
    <div>
        <p>
            <strong>{{ $change->user->name }}</strong>
            changed <strong>{{ $change->field_name }}</strong>
            from <code>{{ $change->old_value }}</code>
            to <code>{{ $change->new_value }}</code>
            on {{ $change->created_at->format('Y-m-d H:i') }}
        </p>
    </div>
@endforeach
```

### Example 3: Audit Dashboard

```php
// Count changes by action this week
$thisWeek = Audit::whereBetween('created_at', [
    now()->startOfWeek(),
    now()->endOfWeek()
])->groupBy('action')->selectRaw('action, COUNT(*) as count')->get();

// Most active users
$topUsers = Audit::groupBy('user_id')->selectRaw('user_id, COUNT(*) as changes')
    ->orderByDesc('changes')
    ->limit(5)
    ->with('user')
    ->get();
```

---

## Troubleshooting / Dépannage

### Changes Not Being Logged

1. ✅ Is the `Auditable` trait added to the model?
2. ✅ Is the user authenticated when making changes?
3. ✅ Run the migration: `php artisan migrate`
4. ✅ Check the `audits` table exists: `php artisan tinker` → `\DB::table('audits')->count()`

### Can't View Audit Logs

1. ✅ Are you an admin user?
2. ✅ Check the policy: `php artisan tinker` → `auth()->user()->can('viewAny', \App\Models\Audit::class)`
3. ✅ Verify routes are registered: `php artisan route:list | grep audit`

### Performance Issues

- The `audits` table can grow large
- Use the cleanup command regularly: `php artisan audit:cleanup --days=90`
- Monitor with: `php artisan audit:cleanup --preview` (no delete)

---

## Security Considerations / Considérations de Sécurité

⚠️ **Audit Trail Integrity**
- Audits cannot be deleted via normal application flow
- Only admins can trigger cleanup via command
- All changes are immutable

⚠️ **User Privacy**
- Only show users their own changes and admin changes
- IP addresses are logged (consider privacy laws)
- Clean up old records according to your retention policy

⚠️ **Performance**
- Audits are indexed for fast queries
- Set up regular cleanup tasks
- Consider archiving very old audits to separate storage

---

## Support / Support

For questions or issues, refer to:
- [Laravel Documentation](https://laravel.com/docs)
- Check the controller: [AuditController](app/Http/Controllers/AuditController.php)
- Review the Trait: [Auditable](app/Traits/Auditable.php)

---

**Last Updated:** February 17, 2026
