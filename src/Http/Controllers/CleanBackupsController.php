<?php

namespace Spatie\BackupTool\Http\Controllers;

use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Tasks\Cleanup\CleanupJob;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;

class CleanBackupsController extends ApiController
{
    public function __invoke(CleanupStrategy $strategy, Config $config)
    {
        $backupDestinations = BackupDestinationFactory::createFromArray($config);

        $cleanupJob = new CleanupJob($backupDestinations, $strategy);

        $cleanupJob->run();

        $this->respondSuccess();
    }
}
