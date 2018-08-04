<?php

namespace Spatie\NovaBackupTool\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Backup\BackupServiceProvider;
use Spatie\NovaBackupTool\NovaBackupToolServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BackupServiceProvider::class,
            NovaBackupToolServiceProvider::class,
        ];
    }
}
