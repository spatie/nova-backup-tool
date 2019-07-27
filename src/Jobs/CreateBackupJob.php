<?php

namespace Spatie\BackupTool\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $option;

    public function __construct($option = '')
    {
        $this->option = $option;
    }

    public function handle()
    {
        $backupJob = BackupJobFactory::createFromArray(config('backup'));

        if ($this->option == 'only-db') {
            $backupJob->dontBackupFilesystem();
        }

        if ($this->option == 'only-files') {
            $backupJob->dontBackupDatabases();
        }

        if (! empty($this->option)) {
            $backupJob->setFilename($this->option.'_'.date('Y-m-d-H-i-s').'.zip');
        }

        $backupJob->run();
    }
}
