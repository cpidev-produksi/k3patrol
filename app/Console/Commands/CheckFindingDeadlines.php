<?php

namespace App\Console\Commands;

use App\Events\FindingNearDeadline;
use App\Events\FindingOverdue;
use App\Models\Finding;
use Illuminate\Console\Command;

class CheckFindingDeadlines extends Command
{
    protected $signature = 'findings:check-deadlines';

    protected $description = 'Kirim notifikasi untuk temuan yang mendekati deadline atau sudah overdue';

    public function handle(): int
    {
        $nearDeadlineCount = 0;
        Finding::query()
            ->nearDeadline() // default threshold 6 jam, lihat Finding::scopeNearDeadline()
            ->whereNull('near_deadline_notified_at')
            ->each(function (Finding $finding) use (&$nearDeadlineCount) {
                event(new FindingNearDeadline($finding));
                $finding->update(['near_deadline_notified_at' => now()]);
                $nearDeadlineCount++;
            });

        $overdueCount = 0;
        Finding::query()
            ->overdue()
            ->whereNull('overdue_notified_at')
            ->each(function (Finding $finding) use (&$overdueCount) {
                event(new FindingOverdue($finding));
                $finding->update(['overdue_notified_at' => now()]);
                $overdueCount++;
            });

        $this->info("Near-deadline notified: {$nearDeadlineCount}, Overdue notified: {$overdueCount}");

        return self::SUCCESS;
    }
}
