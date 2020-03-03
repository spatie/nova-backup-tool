<?php

namespace Spatie\BackupTool\Http\Controllers;

use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Tasks\Cleanup\CleanupJob;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;

class CleanBackupsController extends ApiController
{
    public function __invoke(CleanupStrategy $strategy)
    {
        $backupDestinations = BackupDestinationFactory::createFromArray(config('backup.backup'));

        $cleanupJob = new CleanupJob($backupDestinations, $strategy);

        $cleanupJob->run();

        $this->respondSuccess();
    }
}
