<?php

namespace Spatie\BackupTool\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Backup\BackupServiceProvider;
use Spatie\BackupTool\BackupToolServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BackupServiceProvider::class,
            BackupToolServiceProvider::class,
        ];
    }
}
