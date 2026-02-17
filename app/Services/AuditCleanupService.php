<?php

namespace App\Services;

use App\Models\Audit;
use Carbon\Carbon;

class AuditCleanupService
{
    /**
     * Delete audits older than specified days
     * Default: 90 days
     */
    public function deleteOldAudits(int $daysOld = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);

        return Audit::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Delete audits by action type
     */
    public function deleteAuditsByAction(string $action, int $daysOld = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);

        return Audit::where('action', $action)
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Delete audits for a specific model type
     */
    public function deleteAuditsByModelType(string $modelType, int $daysOld = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);

        return Audit::where('model_type', $modelType)
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Get audit cleanup statistics
     */
    public function getCleanupStats(int $daysOld = 90): array
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);

        return [
            'total_audits' => Audit::count(),
            'audits_to_delete' => Audit::where('created_at', '<', $cutoffDate)->count(),
            'cutoff_date' => $cutoffDate->format('Y-m-d H:i:s'),
            'by_action' => Audit::where('created_at', '<', $cutoffDate)
                ->select('action')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
        ];
    }
}
