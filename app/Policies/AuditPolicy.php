<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;

class AuditPolicy
{
    /**
     * Determine whether the user can view any audits
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the audit
     */
    public function view(User $user, Audit $audit): bool
    {
        // Admins can view all audits
        if ($user->isAdmin()) {
            return true;
        }

        // Users can only view their own changes
        return $audit->user_id === $user->id;
    }

    /**
     * Users cannot delete audits (maintain audit trail integrity)
     */
    public function delete(User $user, Audit $audit): bool
    {
        return false;
    }

    /**
     * Users cannot delete audits
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }
}
