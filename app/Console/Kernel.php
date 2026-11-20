<?php

namespace App\Console;

use App\Events\FindingOverdue;
use App\Models\Finding;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            Finding::overdue()->each(fn (Finding $f) => event(new FindingOverdue($f)));
        })->hourly()->name('check-overdue-findings')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
