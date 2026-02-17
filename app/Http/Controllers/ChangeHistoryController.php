<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use App\Services\ChangeTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChangeHistoryController extends Controller
{
    protected ChangeTrackingService $trackingService;

    public function __construct(ChangeTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
        $this->middleware('auth');
    }

    /**
     * Display audit history for a model
     */
    public function showModelHistory(string $modelType, int $modelId, Request $request)
    {
        // Get the full model class name
        $modelClass = $this->resolveModelClass($modelType);
        
        if (!$modelClass) {
            return response()->json(['error' => 'Invalid model type'], 404);
        }

        $model = $modelClass::findOrFail($modelId);

        // Check authorization
        $this->authorize('view', $model);

        $perPage = $request->query('per_page', 15);
        $history = $this->trackingService->getChangeHistory($model, auth()->user(), $perPage);

        return response()->json([
            'data' => $history->items(),
            'pagination' => [
                'total' => $history->total(),
                'per_page' => $history->perPage(),
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
            ],
        ]);
    }

    /**
     * Display user's change history
     */
    public function showUserChanges(User $user, Request $request)
    {
        // Check authorization - users can only see their own changes unless admin
        if ($user->id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $modelType = $request->query('model_type');
        $perPage = $request->query('per_page', 15);

        $changes = $this->trackingService->getUserChanges($user, $modelType, $perPage);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'data' => $changes->items(),
            'pagination' => [
                'total' => $changes->total(),
                'per_page' => $changes->perPage(),
                'current_page' => $changes->currentPage(),
                'last_page' => $changes->lastPage(),
            ],
        ]);
    }

    /**
     * Display changes to a specific field
     */
    public function showFieldChanges(string $fieldName, Request $request)
    {
        // Only admins can view field-level changes
        $this->authorize('filterByUser', Audit::class);

        $perPage = $request->query('per_page', 15);
        $changes = $this->trackingService->getFieldChanges($fieldName, $perPage);

        return response()->json([
            'field' => $fieldName,
            'data' => $changes->items(),
            'pagination' => [
                'total' => $changes->total(),
                'per_page' => $changes->perPage(),
                'current_page' => $changes->currentPage(),
                'last_page' => $changes->lastPage(),
            ],
        ]);
    }

    /**
     * Display changes within a date range
     */
    public function showChangesByDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $perPage = $request->query('per_page', 15);

        // Non-admins can only see their own changes
        $user = auth()->user()->isAdmin() ? null : auth()->user();

        $changes = $this->trackingService->getChangesByDateRange($startDate, $endDate, $user, $perPage);

        return response()->json([
            'date_range' => [
                'start' => $startDate->format('Y-m-d H:i:s'),
                'end' => $endDate->format('Y-m-d H:i:s'),
            ],
            'data' => $changes->items(),
            'pagination' => [
                'total' => $changes->total(),
                'per_page' => $changes->perPage(),
                'current_page' => $changes->currentPage(),
                'last_page' => $changes->lastPage(),
            ],
        ]);
    }

    /**
     * Display detailed comparison for a change
     */
    public function showChangeComparison(Audit $audit)
    {
        // Check authorization
        $this->authorize('view', $audit);

        $comparison = $this->trackingService->getChangeComparison($audit);

        return response()->json($comparison);
    }

    /**
     * Display change summary for a model
     */
    public function showModelSummary(string $modelType, int $modelId)
    {
        $modelClass = $this->resolveModelClass($modelType);
        
        if (!$modelClass) {
            return response()->json(['error' => 'Invalid model type'], 404);
        }

        $model = $modelClass::findOrFail($modelId);

        // Check authorization
        $this->authorize('view', $model);

        $summary = $this->trackingService->getChangeSummary($model);

        return response()->json($summary);
    }

    /**
     * Display change timeline for a model
     */
    public function showChangeTimeline(string $modelType, int $modelId, Request $request)
    {
        $modelClass = $this->resolveModelClass($modelType);
        
        if (!$modelClass) {
            return response()->json(['error' => 'Invalid model type'], 404);
        }

        $model = $modelClass::findOrFail($modelId);

        // Check authorization
        $this->authorize('view', $model);

        $limit = $request->query('limit', 50);
        $timeline = $this->trackingService->getChangeTimeline($model, $limit);

        return response()->json([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Display most active users (admin only)
     */
    public function showMostActiveUsers(Request $request)
    {
        $this->authorize('viewAny', Audit::class);

        $limit = $request->query('limit', 10);
        $since = $request->query('since') ? Carbon::parse($request->since) : null;

        $users = $this->trackingService->getMostActiveUsers($limit, $since);

        return response()->json([
            'since' => $since?->format('Y-m-d H:i:s'),
            'data' => $users,
        ]);
    }

    /**
     * Display most changed models (admin only)
     */
    public function showMostChangedModels(Request $request)
    {
        $this->authorize('viewAny', Audit::class);

        $limit = $request->query('limit', 10);

        $models = $this->trackingService->getMostChangedModels($limit);

        return response()->json([
            'data' => $models,
        ]);
    }

    /**
     * Export audit logs (admin only)
     */
    public function exportAudits(Request $request)
    {
        $this->authorize('export', Audit::class);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'in:csv,json|default:json',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $format = $request->format ?? 'json';

        $audits = Audit::whereBetween('created_at', [$startDate, $endDate])
            ->with(['user:id,name,email', 'auditable'])
            ->get();

        if ($format === 'csv') {
            return $this->exportAsCSV($audits);
        }

        return response()->json([
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'total_records' => $audits->count(),
            'data' => $audits,
        ]);
    }

    /**
     * Resolve model class from short name
     */
    private function resolveModelClass(string $modelType): ?string
    {
        // Handle both full class names and short names
        if (class_exists('App\\Models\\' . $modelType)) {
            return 'App\\Models\\' . $modelType;
        }

        if (class_exists($modelType)) {
            return $modelType;
        }

        return null;
    }

    /**
     * Export audits as CSV
     */
    private function exportAsCSV($audits)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audits_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($audits) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID', 'User', 'Model Type', 'Model ID', 'Action', 'Field', 
                'Old Value', 'New Value', 'IP Address', 'Changed At'
            ]);

            // Data rows
            foreach ($audits as $audit) {
                fputcsv($file, [
                    $audit->id,
                    $audit->user?->name ?? 'N/A',
                    $audit->model_type,
                    $audit->model_id,
                    $audit->action,
                    $audit->field_name,
                    $audit->old_value,
                    $audit->new_value,
                    $audit->ip_address,
                    $audit->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
