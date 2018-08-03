<?php

namespace Spatie\NovaBackupTool\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\NovaBackupTool\NovaBackupToolServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            NovaBackupToolServiceProvider::class,
        ];
    }
}
