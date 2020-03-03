<?php

namespace Spatie\BackupTool\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Backup\BackupServiceProvider;
use Spatie\BackupTool\BackupToolServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $filesystem;

    public function setUp(): void
    {
        $this->filesystem = new Filesystem();

        parent::setUp();

        Storage::fake();

        Route::middlewareGroup('nova', []);
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->initializeTempDirectory();

        $app['config']->set('database.connections.db1', [
            'driver' => 'sqlite',
            'database' => $this->createSQLiteDatabase('database1.sqlite'),
        ]);

        $app['config']->set('database.default', 'db1');

        $app['config']->set('backup.backup.name', 'Laravel');

        $app['config']->set('backup.monitor_backups', [
            [
                'name' => 'Laravel',
                'disks' => ['local'],
            ],
        ]);

        $app['config']->set('backup.backup.source.databases', ['db1']);

        $app['config']->set('mail.driver', 'log');
    }

    protected function getPackageProviders($app)
    {
        return [
            BackupServiceProvider::class,
            BackupToolServiceProvider::class,
        ];
    }

    public function initializeTempDirectory()
    {
        $this->initializeDirectory($this->getTempDirectory());
    }

    protected function createSQLiteDatabase(string $fileName)
    {
        $directory = $this->getTempDirectory().'/'.dirname($fileName);

        $this->filesystem->makeDirectory($directory, 0755, true, true);

        $fullPath = $this->getTempDirectory().'/'.$fileName;

        touch($fullPath);

        return $fullPath;
    }

    protected function getTempDirectory(): string
    {
        return __DIR__.'/temp';
    }

    public function initializeDirectory(string $directory)
    {
        $this->filesystem->deleteDirectory($directory);

        $this->filesystem->makeDirectory($directory);

        $this->addGitignoreTo($directory);
    }

    public function addGitignoreTo(string $directory)
    {
        $fileName = "{$directory}/.gitignore";

        $fileContents = '*'.PHP_EOL.'!.gitignore';

        $this->filesystem->put($fileName, $fileContents);
    }
}
