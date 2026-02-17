<?php

namespace App\Console\Commands;

use App\Models\Audit;
use Illuminate\Console\Command;

class ViewAudits extends Command
{
    protected $signature = 'audit:view {--model-type= : Filter by model type} {--action= : Filter by action (created, updated, deleted)} {--user-id= : Filter by user ID} {--limit=10 : Number of results to show}';

    protected $description = 'View audit logs from the command line';

    public function handle()
    {
        $query = Audit::with('user')->latest();

        if ($this->option('model-type')) {
            $query->where('model_type', $this->option('model-type'));
        }

        if ($this->option('action')) {
            $query->where('action', $this->option('action'));
        }

        if ($this->option('user-id')) {
            $query->where('user_id', $this->option('user-id'));
        }

        $audits = $query->limit($this->option('limit'))->get();

        if ($audits->isEmpty()) {
            $this->info('No audit records found.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'User', 'Model', 'Action', 'Field', 'Old Value', 'New Value', 'Date'],
            $audits->map(function ($audit) {
                return [
                    $audit->id,
                    $audit->user?->name ?? 'System',
                    "{$audit->model_type} #{$audit->model_id}",
                    $audit->action_label,
                    $audit->field_name,
                    substr($audit->old_value ?? 'N/A', 0, 30),
                    substr($audit->new_value ?? 'N/A', 0, 30),
                    $audit->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray()
        );

        return self::SUCCESS;
    }
}
