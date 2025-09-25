<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BackupSetting extends Model
{
    protected $fillable = [
        'backup_location',
        'backup_schedule', 
        'last_backup_at',
        'next_backup_at',
        'auto_backup_enabled'
    ];

    protected $casts = [
        'last_backup_at' => 'datetime',
        'next_backup_at' => 'datetime',
        'auto_backup_enabled' => 'boolean',
    ];

    public function calculateNextBackup()
    {
        $now = Carbon::now();
        
        return match($this->backup_schedule) {
            'weekly' => $now->addWeek(),
            'monthly' => $now->addMonth(),
            default => $now->addWeek()
        };
    }

    public function shouldRunBackup()
    {
        return $this->auto_backup_enabled && 
               $this->next_backup_at && 
               now() >= $this->next_backup_at;
    }
}