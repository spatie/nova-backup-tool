<?php

namespace Spatie\NovaBackupTool\Controllers;

use Illuminate\Http\Request;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;
use Spatie\BackupTool\Controllers\ApiController;
use Spatie\NovaBackupTool\File;
use Illuminate\Support\Facades\File as IlluminateFile;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Routing\Controller;

class CleanBackupsController extends ApiController
{
    public function __invoke(CleanupStrategy $strategy)
    {
        $strategy = app($config['cleanup']['strategy']);

        $cleanupJob = new CleanupJob($backupDestinations, $strategy, $disableNotifications);

        $cleanupJob->run();

        $this->respondSuccess();
    }
}