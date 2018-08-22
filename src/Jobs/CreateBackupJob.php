<?php

namespace Spatie\BackupTool\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class CreateBackupJob
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        $backupJob = BackupJobFactory::createFromArray(config('backup'));

        $backupJob->run();
    }
}
