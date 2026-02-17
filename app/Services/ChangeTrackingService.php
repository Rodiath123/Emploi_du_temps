<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class ChangeTrackingService
{
    /**
     * Get change history for a specific model with visibility rules
     */
    public function getChangeHistory($model, ?User $user = null, int $perPage = 15)
    {
        $query = $model->audits()
            ->with(['user:id,name,email'])
            ->latest('created_at');

        // Apply visibility filters
        if ($user) {
            $query = $this->applyVisibilityFilter($query, $user);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get changes made by a specific user
     */
    public function getUserChanges(User $user, ?string $modelType = null, int $perPage = 15)
    {
        $query = Audit::where('user_id', $user->id)
            ->latest('created_at');

        if ($modelType) {
            $query->where('model_type', $modelType);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get changes to a specific field across all models
     */
    public function getFieldChanges(string $fieldName, int $perPage = 15)
    {
        return Audit::where('field_name', $fieldName)
            ->where('action', 'updated')
            ->with(['user:id,name,email', 'auditable'])
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Get all changes within a date range
     */
    public function getChangesByDateRange(Carbon $startDate, Carbon $endDate, ?User $user = null, int $perPage = 15)
    {
        $query = Audit::whereBetween('created_at', [$startDate, $endDate])
            ->with(['user:id,name,email', 'auditable'])
            ->latest('created_at');

        if ($user) {
            $query->where('user_id', $user->id);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get detailed change comparison
     */
    public function getChangeComparison(Audit $audit): array
    {
        return [
            'id' => $audit->id,
            'action' => $audit->action_label,
            'field' => $audit->field_name,
            'before' => $this->parseValue($audit->old_value),
            'after' => $this->parseValue($audit->new_value),
            'changed_at' => $audit->created_at->format('Y-m-d H:i:s'),
            'changed_by' => $audit->user ? [
                'id' => $audit->user->id,
                'name' => $audit->user->name,
                'email' => $audit->user->email,
            ] : null,
            'ip_address' => $audit->ip_address,
            'user_agent' => $audit->user_agent,
        ];
    }

    /**
     * Get summary of changes for a model
     */
    public function getChangeSummary($model): array
    {
        $audits = $model->audits;

        return [
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'total_changes' => $audits->count(),
            'created_at' => $model->created_at->format('Y-m-d H:i:s'),
            'last_modified' => $model->audits()
                ->where('action', 'updated')
                ->latest()
                ->first()?->created_at?->format('Y-m-d H:i:s'),
            'created_by' => $model->audits()
                ->where('action', 'created')
                ->first()?->user?->name,
            'modified_by_users' => $audits
                ->whereIn('action', ['created', 'updated'])
                ->pluck('user.name')
                ->unique()
                ->values(),
            'changes_by_action' => [
                'created' => $audits->where('action', 'created')->count(),
                'updated' => $audits->where('action', 'updated')->count(),
                'deleted' => $audits->where('action', 'deleted')->count(),
            ],
        ];
    }

    /**
     * Track changes between two model states
     */
    public function trackModelChanges($oldModel, $newModel): array
    {
        $changes = [];
        $fillable = $newModel->getFillable();

        foreach ($fillable as $field) {
            $oldValue = $oldModel->$field ?? null;
            $newValue = $newModel->$field ?? null;

            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get change timeline for a model
     */
    public function getChangeTimeline($model, int $limit = 50): array
    {
        return $model->audits()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($audit) => [
                'timestamp' => $audit->created_at->toIso8601String(),
                'action' => $audit->action_label,
                'field' => $audit->field_name,
                'old_value' => $this->parseValue($audit->old_value),
                'new_value' => $this->parseValue($audit->new_value),
                'user' => $audit->user?->name,
            ])
            ->toArray();
    }

    /**
     * Apply visibility filters based on user permissions
     */
    private function applyVisibilityFilter($query, User $user)
    {
        // Admins see everything
        if ($user->isAdmin()) {
            return $query;
        }

        // Regular users see:
        // 1. Their own changes
        // 2. Changes to models they own (if applicable)
        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('model_id', $user->id); // For changes to their own profile
        });
    }

    /**
     * Parse value from storage format
     */
    private function parseValue(?string $value)
    {
        if (is_null($value)) {
            return null;
        }

        // Try to decode JSON
        if (is_string($value) && in_array($value[0], ['{', '['])) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }

        return $value;
    }

    /**
     * Get most active users (by audit count)
     */
    public function getMostActiveUsers(int $limit = 10, ?Carbon $since = null): array
    {
        $query = Audit::select('user_id')
            ->selectRaw('COUNT(*) as change_count')
            ->with('user:id,name,email')
            ->where('user_id', '!=', null)
            ->groupBy('user_id')
            ->orderBy('change_count', 'desc')
            ->limit($limit);

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        return $query->get()
            ->map(fn ($audit) => [
                'user' => $audit->user,
                'changes' => $audit->change_count,
            ])
            ->toArray();
    }

    /**
     * Get model with highest change frequency
     */
    public function getMostChangedModels(int $limit = 10): array
    {
        return Audit::select('model_type', 'model_id')
            ->selectRaw('COUNT(*) as change_count')
            ->groupBy(['model_type', 'model_id'])
            ->orderBy('change_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn ($audit) => [
                'model_type' => $audit->model_type,
                'model_id' => $audit->model_id,
                'changes' => $audit->change_count,
            ])
            ->toArray();
    }
}
