<?php

namespace Spatie\BackupTool\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class BackupStatusesController extends ApiController
{
    public function index()
    {
        return Cache::remember('backup-statuses', now()->addSeconds(4), function () {
            $monitorConfiguration = (new \ReflectionMethod(BackupDestinationStatusFactory::class, 'createForMonitorConfig'))
                ->getParameters()[0]->getType()->getName() === 'Spatie\Backup\Config\MonitoredBackupsConfig'
                ? \Spatie\Backup\Config\MonitoredBackupsConfig::fromArray(config('backup.monitor_backups'))
                : config('backup.monitor_backups');

            return BackupDestinationStatusFactory::createForMonitorConfig($monitorConfiguration)
                ->map(function (BackupDestinationStatus $backupDestinationStatus) {
                    return [
                        'name' => $backupDestinationStatus->backupDestination()->backupName(),
                        'disk' => $backupDestinationStatus->backupDestination()->diskName(),
                        'reachable' => $backupDestinationStatus->backupDestination()->isReachable(),
                        'healthy' => $backupDestinationStatus->isHealthy(),
                        'amount' => $backupDestinationStatus->backupDestination()->backups()->count(),
                        'newest' => $backupDestinationStatus->backupDestination()->newestBackup()
                            ? $backupDestinationStatus->backupDestination()->newestBackup()->date()->diffForHumans()
                            : __('No backups present'),
                        'usedStorage' => Format::humanReadableSize($backupDestinationStatus->backupDestination()->usedStorage()),
                    ];
                })
                ->values()
                ->toArray();
        });
    }
}
