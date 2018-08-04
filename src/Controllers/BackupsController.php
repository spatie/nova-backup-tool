<?php

namespace Spatie\NovaBackupTool\Controllers;

use App\Rules\BackupDisk;
use Illuminate\Http\Request;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;
use Spatie\BackupTool\Controllers\ApiController;
use Spatie\NovaBackupTool\File;

class BackupsController extends ApiController
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'disk' => new BackupDisk(),
        ]);

        $backupDestination = BackupDestination::create($validated['disk'], config('backup.name'));

        return $backupDestination
            ->backups()
            ->map(function (Backup $backup) {
                return [
                    'path' => $backup->path(),
                    'date' => $backup->date(),
                    'size' => $backup->size(),
                ];
            })
            ->toArray();
    }

    public function create()
    {
        $backupJob = BackupJobFactory::createFromArray(config('backup'));

        $backupJob->run();
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'disk' => new BackupDisk(),
            'path' => 'required',
        ]);

        $backupDestination = BackupDestination::create($validated['disk'], config('backup.name'));

        $backupDestination
            ->backups()
            ->first(function (Backup $backup) use ($validated) {
                $backup->path() === $validated['path'];
            })
            ->delete();

        $this->respondSuccess();
    }
}