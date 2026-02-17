<?php

/**
 * QUICK START GUIDE - Change Tracking Module
 * 
 * This file shows how to implement the change tracking in your models
 */

// ============================================
// STEP 1: Add Auditable Trait to Models
// ============================================

// Example 1: Schedule Model
// File: app/Models/Schedule.php
/*
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Schedule extends Model
{
    use Auditable;
    
    protected $fillable = ['name', 'description', 'status'];
}
?>
*/

// Example 2: Event Model
// File: app/Models/Event.php
/*
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Event extends Model
{
    use Auditable;
    
    protected $fillable = ['title', 'description', 'start_date', 'end_date'];
}
?>
*/

// Example 3: Class Model
// File: app/Models/ClassModel.php
/*
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ClassModel extends Model
{
    use Auditable;
    
    protected $table = 'classes';
    protected $fillable = ['name', 'code', 'description', 'schedule_id'];
}
?>
*/

// ============================================
// STEP 2: Use in Controllers
// ============================================

// Example Controller Method
/*
<?php
namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function show(Schedule $schedule)
    {
        // Get change history
        $history = $schedule->getAuditHistory(10);
        
        // Get last modification info
        $lastChange = $schedule->getLastModification();
        
        return view('schedule.show', [
            'schedule' => $schedule,
            'history' => $history,
            'lastChange' => $lastChange,
        ]);
    }
    
    public function update(Request $request, Schedule $schedule)
    {
        // Update is automatically tracked
        $schedule->update($request->validated());
        
        return redirect()->back()->with('success', 'Updated!');
    }
}
?>
*/

// ============================================
// STEP 3: Display Changes in Views
// ============================================

// Example Blade Template
/*
<div>
    <h2>Change History</h2>
    
    @foreach($history as $change)
        <div class="change-item">
            <p>
                <strong>{{ $change->user->name }}</strong>
                {{ $change->action_label }}
                <strong>{{ $change->field_name }}</strong>
                from <code>{{ $change->old_value }}</code>
                to <code>{{ $change->new_value }}</code>
                on {{ $change->created_at->format('Y-m-d H:i') }}
            </p>
        </div>
    @endforeach
    
    <div>{{ $history->links() }}</div>
</div>
*/

// ============================================
// STEP 4: Access Control
// ============================================

// In any template or controller:
/*
@can('viewAny', \App\Models\Audit::class)
    <a href="{{ route('audits.index') }}">View All Audits</a>
@endcan

@can('view', $audit)
    <!-- User can see this specific audit -->
@endcan
*/

// ============================================
// STEP 5: Console Commands
// ============================================

// View audits from command line
/*
php artisan audit:view
php artisan audit:view --model-type="App\Models\Schedule"
php artisan audit:view --action=updated --limit=20
*/

// Clean up old records
/*
php artisan audit:cleanup --days=90
*/

// ============================================
// STEP 6: Manual Audit Queries
// ============================================

// Example queries in your application
/*
use App\Models\Audit;

// Get all audits for a specific model
$schedule = Schedule::find(1);
$audits = $schedule->audits()->get();

// Get all updates to a field
$audits = Audit::where('field_name', 'name')
    ->where('model_type', 'App\Models\Schedule')
    ->get();

// Get changes in a date range
$audits = Audit::whereBetween('created_at', [
    now()->subDays(7),
    now()
])->get();

// Get user's changes
$userChanges = Audit::where('user_id', auth()->id())->get();
*/

// ============================================
// IMPORTANT: Update Your Route Guards
// ============================================

// In your controllers, add these checks:
/*
public function listAudits(Request $request)
{
    // This checks if user can view any audits (admin role required)
    $this->authorize('viewAny', Audit::class);
    
    // ... rest of code
}

public function viewAudit(Audit $audit)
{
    // This checks if user can view this specific audit
    $this->authorize('view', $audit);
    
    // ... rest of code
}
*/

// ============================================
// TIPS & BEST PRACTICES
// ============================================

/*
1. MODELS TO TRACK:
   - Add Auditable to core models: User, Schedule, Event, Class
   - Don't add to temporary/cache models
   - Optional: Track settings/configurations

2. PERFORMANCE:
   - Audits are automatically indexed
   - Run cleanup regularly: php artisan audit:cleanup
   - Monitor database size

3. PRIVACY:
   - Be aware that IP addresses are logged
   - Clean up records according to your retention policy
   - Make sure only authorized users see the audit log

4. DEBUGGING:
   - Use audit:view command to investigate issues
   - Check the audits table directly for verification
   - Use export to analyze trends

5. CUSTOMIZATION:
   - Modify isAdmin() in User model if using different role system
   - Adjust cleanup retention based on compliance needs
   - Customize views to match your UI
*/

// ============================================
// MIGRATIONS: If you need to customize further
// ============================================

// The audits table structure:
/*
CREATE TABLE audits (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NULL REFERENCES users(id),
    model_type VARCHAR(255),      -- e.g., "App\Models\Schedule"
    model_id BIGINT,              -- e.g., 1
    action VARCHAR(255),          -- created, updated, deleted
    field_name VARCHAR(255) NULL, -- e.g., "name"
    old_value LONGTEXT NULL,      -- previous value
    new_value LONGTEXT NULL,      -- new value
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX (model_type, model_id),
    INDEX (user_id),
    INDEX (created_at)
);
*/
