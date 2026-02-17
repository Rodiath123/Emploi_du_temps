# Module de Suivi des Modifications - Setup Checklist

## 📋 Installation Checklist

### ✅ Step 1: Run Migration
```bash
php artisan migrate
```
This creates the `audits` table in your database.

**Files created:**
- `database/migrations/2026_02_17_000000_create_audits_table.php`

---

### ✅ Step 2: Add Auditable Trait to Models
Add the `Auditable` trait to any model you want to track changes for.

**Example:**
```php
use App\Traits\Auditable;

class Schedule extends Model
{
    use Auditable;
    protected $fillable = ['name', 'description'];
}
```

**Already modified:**
- `app/Models/User.php` - User model updated with trait

---

### ✅ Step 3: Configure AuthServiceProvider
The policy has been registered automatically.

**Files created:**
- `app/Policies/AuditPolicy.php` - Policy for authorization

**Files modified:**
- `app/Providers/AuthServiceProvider.php` - Registered AuditPolicy

---

### ✅ Step 4: Update User Model (if needed)
The `isAdmin()` method has been added to determine user roles.

**Files modified:**
- `app/Models/User.php` - Added Auditable trait and isAdmin() method

**Note:** Adjust the `isAdmin()` logic based on your user role system:
```php
public function isAdmin(): bool
{
    return $this->role === 'admin'; // Adjust based on your system
}
```

---

## 📁 Files Created

### Core Files
| File | Purpose |
|------|---------|
| `app/Models/Audit.php` | Audit model with scopes and relations |
| `app/Traits/Auditable.php` | Trait to add to models for auto-tracking |
| `app/Services/AuditCleanupService.php` | Service for managing old records |

### Controllers & Policies
| File | Purpose |
|------|---------|
| `app/Http/Controllers/AuditController.php` | Controller for viewing audits |
| `app/Policies/AuditPolicy.php` | Authorization policy |

### Console Commands
| File | Purpose |
|------|---------|
| `app/Console/Commands/CleanupAudits.php` | Command to delete old audits |
| `app/Console/Commands/ViewAudits.php` | Command to view audits via CLI |

### Views
| File | Purpose |
|------|---------|
| `resources/views/audits/index.blade.php` | List all audits with filters |
| `resources/views/audits/show.blade.php` | Show change history for item |

### Documentation
| File | Purpose |
|------|---------|
| `AUDIT_DOCUMENTATION.md` | Complete documentation |
| `QUICK_START_AUDIT.php` | Quick start guide with examples |
| `SETUP_CHECKLIST.md` | This file |

### Database
| File | Purpose |
|------|---------|
| `database/migrations/2026_02_17_..._create_audits_table.php` | Audits table schema |

---

## 🔧 Configuration Steps

### 1. Update User Model isAdmin() Logic
**File:** `app/Models/User.php`

Review and adjust the `isAdmin()` method based on your user role system:

```php
// Current implementation:
public function isAdmin(): bool
{
    return isset($this->attributes['role']) && $this->attributes['role'] === 'admin'
           || isset($this->attributes['is_admin']) && $this->attributes['is_admin'];
}

// Change to match your system:
// If using role column: return $this->role === 'admin';
// If using boolean: return $this->is_admin === true;
// If using permissions: return $this->hasPermission('view_audits');
```

### 2. Add Auditable Trait to Models
For each model you want to track:

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class YourModel extends Model
{
    use Auditable;
    
    protected $fillable = ['field1', 'field2');
}
```

**Models to consider tracking:**
- ✅ User (already done)
- [ ] Schedule
- [ ] Event  
- [ ] Class
- [ ] Other domain models

### 3. Register Routes (Already Done)
Routes have been added to `routes/web.php`:

```
/audits              - View all audits (admin only)
/audits/show         - View history for a specific model
/audits/export       - Export audits to CSV
```

### 4. Schedule Cleanup (Optional)
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Clean up audits older than 90 days, monthly
    $schedule->command('audit:cleanup', ['--days' => 90])
        ->monthly();
}
```

---

## 🚀 Quick Test

Run these commands to verify everything works:

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Test CLI Commands
```bash
# View audits
php artisan audit:view

# View with filter
php artisan audit:view --action=created

# See cleanup stats
php artisan audit:cleanup --days=90
```

### 3. Test in Tinker
```bash
php artisan tinker

> $user = \App\Models\User::first();
> $user->update(['name' => 'Test Update']);
> \App\Models\Audit::latest()->first();
```

### 4. Access Web Interface
Navigate to: `http://yourapp.com/audits`

---

## 📖 Documentation

### Quick Reference
- **Quick Start:** [QUICK_START_AUDIT.php](QUICK_START_AUDIT.php)
- **Full Docs:** [AUDIT_DOCUMENTATION.md](AUDIT_DOCUMENTATION.md)

### Key Commands
```bash
# View audits from CLI
php artisan audit:view

# Clean old records
php artisan audit:cleanup --days=90

# See cleanup stats without deleting
php artisan audit:cleanup --days=90

# Access web interface
http://yourapp.com/audits
```

---

## ⚠️ Important Notes

### 1. User Model Changes
The User model now includes:
- `Auditable` trait (auto-tracks all user changes)
- `isAdmin()` method (needs adjustment for your role system)

### 2. Database Retention
Configure based on your compliance needs:
- 90 days: Standard (default setup)
- 365 days: Long-term tracking
- Custom: Adjust cleanup script

### 3. Performance
- Audits table is indexed for fast queries
- Run cleanup regularly to manage database size
- Use `audit:cleanup --preview` to see what will be deleted

### 4. Security
- Audits cannot be deleted via normal app flow
- Only admins can trigger cleanup
- IP addresses are logged
- Privacy: Configure according to local regulations

---

## 🔍 Troubleshooting

### Migration Failed
```bash
# Check database connection
php artisan tinker
> DB::connection()->getPdo();

# Check existing tables
> DB::table('audits')->count();
```

### Changes Not Being Recorded
1. Verify model has `Auditable` trait
2. Ensure user is authenticated
3. Check `fillable` array is defined
4. Verify migration ran: `php artisan migrate:status`

### Can't Access /audits
1. Check authentication: `auth()->check()`
2. Verify user is admin: `auth()->user()->isAdmin()`
3. Check routes: `php artisan route:list | grep audits`

---

## 📝 Next Steps

1. ✅ Run migration: `php artisan migrate`
2. ⬜ Add Auditable trait to core models
3. ⬜ Adjust User model isAdmin() for your role system
4. ⬜ Test with tinker
5. ⬜ Access `/audits` in your browser
6. ⬜ Schedule cleanup task in Kernel.php

---

## 📞 Support

For questions, refer to:
- [Core Documentation](#-documentation)
- [Laravel Docs](https://laravel.com)
- [PHP Code Comments](app/Traits/Auditable.php)

---

**Last Updated:** February 17, 2026
**Status:** ✅ Ready for Implementation
