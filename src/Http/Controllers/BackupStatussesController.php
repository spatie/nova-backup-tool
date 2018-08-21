<?php

namespace Spatie\BackupTool\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Spatie\BackupTool\File;

class BackupStatussesController extends ApiController
{
    public function index()
    {
        return Cache::remember('backup-statusses', 1 / 15, function () {
            return BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitorBackups'))
                ->map(function (BackupDestinationStatus $backupDestinationStatus) {
                    return [
                        'name' => $backupDestinationStatus->backupName(),
                        'disk' => $backupDestinationStatus->diskName(),
                        'reachable' => $backupDestinationStatus->isReachable(),
                        'healthy' => $backupDestinationStatus->isHealthy(),
                        'amount' => $backupDestinationStatus->amountOfBackups(),
                        'newest' => $backupDestinationStatus->dateOfNewestBackup()
                            ? $backupDestinationStatus->dateOfNewestBackup()->diffForHumans()
                            : 'No backups present',
                        'usedStorage' => $backupDestinationStatus->humanReadableUsedStorage(),
                    ];
                })
                ->toArray();
        });
    }
}
