<?php

namespace App\Console\Commands;

use App\Services\AuditCleanupService;
use Illuminate\Console\Command;

class CleanupAudits extends Command
{
    protected $signature = 'audit:cleanup {--days=90 : Delete audits older than X days}';

    protected $description = 'Delete old audit records to maintain database performance';

    public function handle()
    {
        $days = $this->option('days');

        $this->info("Fetching cleanup statistics for audits older than {$days} days...");

        $service = new AuditCleanupService();
        $stats = $service->getCleanupStats($days);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Audits', $stats['total_audits']],
                ['Audits to Delete', $stats['audits_to_delete']],
                ['Cutoff Date', $stats['cutoff_date']],
            ]
        );

        if ($stats['audits_to_delete'] === 0) {
            $this->info('No audits to delete.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Do you want to delete these audits?')) {
            $this->info('Cleanup cancelled.');
            return self::SUCCESS;
        }

        $deleted = $service->deleteOldAudits($days);

        $this->info("✓ Successfully deleted {$deleted} audit records.");

        return self::SUCCESS;
    }
}
