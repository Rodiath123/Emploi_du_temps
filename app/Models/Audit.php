<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable()
    {
        return $this->morphTo('model');
    }

    /**
     * Scope: Filter by model type
     */
    public function scopeForModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope: Filter by model ID
     */
    public function scopeForModelId($query, int $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    /**
     * Scope: Filter by action
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get human-readable action name
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => __('Created'),
            'updated' => __('Updated'),
            'deleted' => __('Deleted'),
            default => ucfirst($this->action),
        };
    }

    /**
     * Check if change is visible to user
     */
    public function isVisibleTo($user): bool
    {
        // Admins can see all audits
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Users can only see their own changes and changes to their own data
        if ($user && $this->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Scope: Get recent changes
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope: Get changes excluding sensitive fields
     */
    public function scopeExcludeSensitive($query)
    {
        $sensitiveFields = ['password', 'api_token', 'secret', 'token', 'remember_token'];
        return $query->whereNotIn('field_name', $sensitiveFields);
    }

    /**
     * Scope: Get only field changes (exclude meta actions)
     */
    public function scopeOnlyFieldChanges($query)
    {
        return $query->whereNotNull('field_name');
    }

    /**
     * Get formatted old value
     */
    public function getFormattedOldValueAttribute(): mixed
    {
        return $this->formatValue($this->old_value);
    }

    /**
     * Get formatted new value
     */
    public function getFormattedNewValueAttribute(): mixed
    {
        return $this->formatValue($this->new_value);
    }

    /**
     * Get change description
     */
    public function getChangeDescriptionAttribute(): string
    {
        if ($this->action === 'created') {
            return "Record created";
        }

        if ($this->action === 'deleted') {
            return "Record deleted";
        }

        if ($this->action === 'updated' && $this->field_name) {
            return "Changed {$this->field_name} from '{$this->old_value}' to '{$this->new_value}'";
        }

        return ucfirst($this->action);
    }

    /**
     * Check if this is a sensitive field change
     */
    public function isSensitiveField(): bool
    {
        $sensitiveFields = ['password', 'api_token', 'secret', 'token', 'remember_token'];
        return in_array(strtolower($this->field_name ?? ''), $sensitiveFields);
    }

    /**
     * Format value for display
     */
    private function formatValue(?string $value): mixed
    {
        if (is_null($value)) {
            return null;
        }

        // Try to decode JSON
        if (is_string($value) && in_array($value[0] ?? '', ['{', '['])) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }

        return $value;
    }

    /**
     * Get computed difference
     */
    public function getDifferenceAttribute(): ?array
    {
        if (!$this->field_name || $this->action !== 'updated') {
            return null;
        }

        return [
            'field' => $this->field_name,
            'from' => $this->formatted_old_value,
            'to' => $this->formatted_new_value,
        ];
    }


        return false;
    }
}
