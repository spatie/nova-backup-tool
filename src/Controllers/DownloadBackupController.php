<?php

namespace Spatie\BackupTool\Controllers;

use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Symfony\Component\HttpFoundation\Request;

class DownloadBackupController extends ApiController
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'disk' => new BackupDisk(),
            'path' => 'required',
        ]);

        $backupDestination = BackupDestination::create($validated['disk'], config('backup.name'));

        return $backupDestination->backups()->first(function() {

        });
    }
}