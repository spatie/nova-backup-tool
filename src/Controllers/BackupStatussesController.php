<?php

namespace Spatie\BackupTool\Controllers;

use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Spatie\BackupTool\File;

class BackupStatussesController extends ApiController
{
    public function index()
    {
        return BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitorBackups'))
            ->map(function (BackupDestinationStatus $backupDestinationStatus) {
                return [
                    'name' => $backupDestinationStatus->backupName(),
                    'disk' => $backupDestinationStatus->diskName(),
                    'reachable' => $backupDestinationStatus->isReachable(),
                    'healthy' => $backupDestinationStatus->isHealthy(),
                    'amount' => $backupDestinationStatus->amountOfBackups(),
                    'newest' => $backupDestinationStatus->dateOfNewestBackup()
                        ? Format::ageInDays($backupDestinationStatus->dateOfNewestBackup())
                        : 'No backups present',
                    'usedStorage' => $backupDestinationStatus->humanReadableUsedStorage(),
                ];
            })
            ->toArray();
    }
}