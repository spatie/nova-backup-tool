<?php

namespace Spatie\BackupTool\Jobs;

use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class CreateBackupJob
{
    public function handle()
    {
        $backupJob = BackupJobFactory::createFromArray(config('backup'));

        $backupJob->run();
    }
}