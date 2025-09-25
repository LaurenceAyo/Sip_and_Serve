<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackupSetting;
use App\Http\Controllers\BackupSettingsController;
use Illuminate\Support\Facades\Storage;

class AutoBackup extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Run automatic backups based on schedule settings';

    public function handle()
    {
        $settings = BackupSetting::first();
        
        if (!$settings || !$settings->shouldRunBackup()) {
            $this->info('No backup needed at this time.');
            return;
        }

        try {
            $controller = new BackupSettingsController();
            $backup = $controller->generateBackupData();
            
            $filename = 'auto_backup_' . now()->format('Y-m-d_H-i-s') . '.json';
            Storage::put('backups/' . $filename, json_encode($backup));
            
            $settings->update([
                'last_backup_at' => now(),
                'next_backup_at' => $settings->calculateNextBackup()
            ]);
            
            $this->info("Backup created successfully: $filename");
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
        }
    }
}