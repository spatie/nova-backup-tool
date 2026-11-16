<?php

namespace Spatie\BackupTool\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Helpers\Format;
use Spatie\BackupTool\Jobs\CreateBackupJob;
use Spatie\BackupTool\Rules\BackupDisk;
use Spatie\BackupTool\Rules\PathToZip;

class BackupsController extends ApiController
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'disk' => ['required', new BackupDisk],
        ]);

        $backupDestination = BackupDestination::create($validated['disk'], config('backup.backup.name'));

        return Cache::remember("backups-{$validated['disk']}", now()->addSeconds(4), function () use ($backupDestination) {
            return $backupDestination
                ->backups()
                ->map(function (Backup $backup) {
                    $size = method_exists($backup, 'sizeInBytes') ? $backup->sizeInBytes() : $backup->size();

                    return [
                        'path' => $backup->path(),
                        'date' => $backup->date()->format('Y-m-d H:i:s'),
                        'size' => Format::humanReadableSize($size),
                    ];
                })
                ->toArray();
        });
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'option' => ['nullable', 'string', Rule::in(['only-db', 'only-files'])],
        ]);

        dispatch(new CreateBackupJob($validated['option'] ?? ''))
            ->onQueue(config('nova-backup-tool.queue'));
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'disk' => ['required', 'string', new BackupDisk],
            'path' => ['bail', 'required', 'string', new PathToZip],
        ]);

        $backupDestination = BackupDestination::create($validated['disk'], config('backup.backup.name'));

        $backup = $backupDestination
            ->backups()
            ->first(function (Backup $backup) use ($validated) {
                return $backup->path() === $validated['path'];
            });

        if (! $backup) {
            return response('Backup not found', 422);
        }

        $backup->delete();

        Cache::forget("backups-{$validated['disk']}");
        Cache::forget('backup-statuses');

        return $this->respondSuccess();
    }
}
