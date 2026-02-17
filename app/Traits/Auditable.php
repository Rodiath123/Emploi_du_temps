<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    /**
     * Boot the Auditable trait
     */
    public static function bootAuditable()
    {
        // Track on create
        static::created(function ($model) {
            self::recordAudit($model, 'created');
        });

        // Track on update
        static::updated(function ($model) {
            self::recordAudit($model, 'updated');
        });

        // Track on delete
        static::deleted(function ($model) {
            self::recordAudit($model, 'deleted');
        });
    }

    /**
     * Get the audits for this model
     */
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'model');
    }

    /**
     * Record audit entry
     */
    protected static function recordAudit($model, $action)
    {
        $userId = auth()->check() ? auth()->id() : null;
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        if ($action === 'created') {
            // For create action, record all fillable attributes
            foreach ($model->getFillable() as $field) {
                Audit::create([
                    'user_id' => $userId,
                    'model_type' => get_class($model),
                    'model_id' => $model->id,
                    'action' => $action,
                    'change_type' => 'field',
                    'field_name' => $field,
                    'old_value' => null,
                    'new_value' => $model->$field,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'metadata' => self::getChangeMetadata($model, $action, $field),
                ]);
            }
        } elseif ($action === 'updated') {
            // For update action, only record changed fields
            foreach ($model->getChanges() as $field => $newValue) {
                // Skip timestamps
                if (in_array($field, ['created_at', 'updated_at'])) {
                    continue;
                }

                $oldValue = $model->getOriginal($field);

                Audit::create([
                    'user_id' => $userId,
                    'model_type' => get_class($model),
                    'model_id' => $model->id,
                    'action' => $action,
                    'change_type' => 'field',
                    'field_name' => $field,
                    'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
                    'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'metadata' => self::getChangeMetadata($model, $action, $field),
                ]);
            }
        } elseif ($action === 'deleted') {
            // For delete action, record the deletion
            Audit::create([
                'user_id' => $userId,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'action' => $action,
                'change_type' => 'meta',
                'field_name' => null,
                'old_value' => null,
                'new_value' => null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'metadata' => self::getChangeMetadata($model, $action),
            ]);
        }
    }

    /**
     * Get metadata for a change
     */
    protected static function getChangeMetadata($model, string $action, ?string $field = null): ?array
    {
        return json_encode([
            'model_class' => get_class($model),
            'action' => $action,
            'field' => $field,
            'timestamp' => now()->toIso8601String(),
            'request_method' => request()->method(),
            'request_path' => request()->path(),
        ]);
    }

    /**
     * Get audit history for this model with pagination
     */
    public function getAuditHistory($perPage = 15)
    {
        return $this->audits()
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get last modification details
     */
    public function getLastModification()
    {
        return $this->audits()
            ->whereIn('action', ['created', 'updated'])
            ->latest()
            ->first();
    }
}
