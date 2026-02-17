<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Audit;

/**
 * Test Suite for Audit Tracking System
 * 
 * Run with: php artisan test --filter=AuditTest
 */
class AuditTest extends TestCase
{
    /**
     * Test that audits are created when a model is created
     */
    public function test_audit_created_on_model_create(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $audits = Audit::where('action', 'created')
            ->where('model_type', User::class)
            ->get();

        $this->assertTrue($audits->count() > 0);
    }

    /**
     * Test that audits are created when a model is updated
     */
    public function test_audit_created_on_model_update(): void
    {
        $user = User::factory()->create();
        $originalName = $user->name;

        $user->update(['name' => 'Updated Name']);

        $audit = Audit::where('action', 'updated')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('field_name', 'name')
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals($originalName, $audit->old_value);
        $this->assertEquals('Updated Name', $audit->new_value);
    }

    /**
     * Test that audits are created when a model is deleted
     */
    public function test_audit_created_on_model_delete(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $audit = Audit::where('action', 'deleted')
            ->where('model_type', User::class)
            ->where('model_id', $userId)
            ->first();

        $this->assertNotNull($audit);
    }

    /**
     * Test audit scopes
     */
    public function test_audit_scopes(): void
    {
        $user = User::factory()->create();
        $user->update(['name' => 'Updated']);

        $audits = Audit::forModel(User::class)
            ->forModelId($user->id)
            ->get();

        $this->assertTrue($audits->count() > 0);

        $updateAudits = Audit::forAction('updated')->get();
        $this->assertTrue($updateAudits->count() > 0);
    }

    /**
     * Test getAuditHistory method on model
     */
    public function test_get_audit_history(): void
    {
        $user = User::factory()->create();
        $user->update(['name' => 'Updated']);

        $history = $user->getAuditHistory();

        $this->assertTrue($history->count() > 0);
    }

    /**
     * Test getLastModification method on model
     */
    public function test_get_last_modification(): void
    {
        $user = User::factory()->create();
        $user->update(['name' => 'Updated']);

        $last = $user->getLastModification();

        $this->assertNotNull($last);
        $this->assertEquals('name', $last->field_name);
    }

    /**
     * Test audit policy - admins can view all
     */
    public function test_admin_can_view_audits(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $audit = Audit::factory()->create();

        $this->assertTrue($admin->can('view', $audit));
    }

    /**
     * Test audit policy - users can only view their own
     */
    public function test_user_can_only_view_own_audits(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $audit = Audit::factory()->create(['user_id' => $user1->id]);

        $this->assertTrue($user1->can('view', $audit));
        $this->assertFalse($user2->can('view', $audit));
    }

    /**
     * Test IP address and user agent are recorded
     */
    public function test_ip_and_user_agent_recorded(): void
    {
        $user = User::factory()->create();
        $user->update(['name' => 'Updated']);

        $audit = Audit::where('model_id', $user->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($audit->ip_address);
    }
}
