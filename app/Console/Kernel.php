<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CalculateStats;

class Kernel extends ConsoleKernel
{
    protected $commands = [

		// Recalculate Stats!
		'App\Console\Commands\CalculateStats',

        // Existing Count Clear
        'App\Console\Commands\ClearExistingCount'

    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:calculateStats')
        ->dailyAt('00:00')
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/scheduler.log'));

        $schedule->command('command:clearExistingCount')
        ->monthly()
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}