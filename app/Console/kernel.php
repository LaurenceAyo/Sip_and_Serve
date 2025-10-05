<?php

namespace App\Console;

use App\Models\BackupSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $settings = BackupSetting::first();

        if ($settings && $settings->backup_schedule === 'weekly') {
            $schedule->command('backup:auto')
                ->weekly()
                ->mondays()
                ->at('08:00');
        }

        if ($settings && $settings->backup_schedule === 'monthly') {
            $schedule->command('backup:auto')
                ->monthly()
                ->at('08:00');
        }
        
        // Optional: Add daily option if needed
        if ($settings && $settings->backup_schedule === 'daily') {
            $schedule->command('backup:auto')
                ->daily()
                ->at('08:00');
        }
    }

    protected $commands = [
        \App\Console\Commands\AutoBackup::class,
    ];
}