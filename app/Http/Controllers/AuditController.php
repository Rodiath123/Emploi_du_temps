<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * List all audits (admin only)
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Audit::class);

        $query = Audit::with('user')
            ->latest('created_at');

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange(
                $request->date_from,
                $request->date_to
            );
        }

        // Search in field names or values
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('field_name', 'like', "%{$search}%")
                    ->orWhere('old_value', 'like', "%{$search}%")
                    ->orWhere('new_value', 'like', "%{$search}%");
            });
        }

        $audits = $query->paginate(20);

        return view('audits.index', [
            'audits' => $audits,
            'modelTypes' => $this->getModelTypes(),
            'actions' => ['created', 'updated', 'deleted'],
        ]);
    }

    /**
     * View audit history for a specific model
     */
    public function show(Request $request): View
    {
        $modelType = $request->query('model_type');
        $modelId = $request->query('model_id');

        $audits = Audit::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user')
            ->latest()
            ->paginate(15);

        // Check authorization
        foreach ($audits as $audit) {
            $this->authorize('view', $audit);
        }

        return view('audits.show', [
            'audits' => $audits,
            'modelType' => $modelType,
            'modelId' => $modelId,
        ]);
    }

    /**
     * Export audit logs (admin only)
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Audit::class);

        $query = Audit::with('user')->latest();

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $audits = $query->get();

        $filename = 'audits_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://memory', 'r+');

        // CSV Headers
        fputcsv($handle, [
            'ID',
            'User',
            'Model Type',
            'Model ID',
            'Action',
            'Field',
            'Old Value',
            'New Value',
            'IP Address',
            'Date',
        ]);

        // CSV Data
        foreach ($audits as $audit) {
            fputcsv($handle, [
                $audit->id,
                $audit->user?->name ?? 'System',
                $audit->model_type,
                $audit->model_id,
                $audit->action_label,
                $audit->field_name,
                $audit->old_value,
                $audit->new_value,
                $audit->ip_address,
                $audit->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()
            ->streamDownload(
                function () use ($csv) {
                    echo $csv;
                },
                $filename,
                [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]
            );
    }

    /**
     * Get available model types
     */
    private function getModelTypes(): array
    {
        return Audit::query()
            ->distinct('model_type')
            ->pluck('model_type')
            ->sort()
            ->values()
            ->toArray();
    }
}
