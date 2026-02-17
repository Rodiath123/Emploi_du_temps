<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;

class ChangeVisibilityPolicy
{
    /**
     * Determine if the user can view any audit changes
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view a specific audit change
     */
    public function view(User $user, Audit $audit): bool
    {
        // Admins can view all audits
        if ($user->isAdmin()) {
            return true;
        }

        // Users can view their own changes
        if ($audit->user_id === $user->id) {
            return true;
        }

        // Users can view changes to their own profile
        if ($audit->model_id === $user->id && $audit->model_type === User::class) {
            return true;
        }

        // Owner can view changes to their own data
        if (method_exists($audit->auditable(), 'isOwnedBy') && $audit->auditable()->isOwnedBy($user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can export audit logs
     */
    public function export(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can filter changes by user
     */
    public function filterByUser(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine visibility level for sensitive fields
     */
    public function canViewField(User $user, Audit $audit, string $fieldName): bool
    {
        // Admins see everything
        if ($user->isAdmin()) {
            return true;
        }

        // Define sensitive fields
        $sensitiveFields = ['password', 'api_token', 'secret', 'token'];

        // Users cannot see sensitive fields in other users' changes
        if (in_array(strtolower($fieldName), $sensitiveFields)) {
            return $audit->user_id === $user->id;
        }

        return $this->view($user, $audit);
    }

    /**
     * Get filterable options for the user
     */
    public function getFilterOptions(User $user): array
    {
        $options = [
            'by_date_range' => true,
            'by_action_type' => true,
        ];

        if ($user->isAdmin()) {
            $options['by_user'] = true;
            $options['by_model_type'] = true;
            $options['by_field'] = true;
        }

        return $options;
    }
}
