<?php

namespace Spatie\BackupTool\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Workbench\Database\Factories\UserFactory;

abstract class TestCase extends Orchestra
{
    use DatabaseMigrations;
    use WithWorkbench;

    /** @var array<int, string> */
    protected $connectionsToTransact = ['testing'];

    /** {@inheritDoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        $app['config']->set('backup.backup.name', 'Laravel');

        $app['config']->set('backup.monitor_backups', [
            [
                'name' => 'Laravel',
                'disks' => ['local'],
                'health_checks' => [],
            ],
        ]);

        $app['config']->set('backup.backup.source.databases', ['testing']);

        $app['config']->set('mail.driver', 'log');
    }

    protected function createBackup()
    {
        $this
            ->actingAs(UserFactory::new()->create())
            ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        $this->artisan('queue:work', ['--once' => true])->run();

        Storage::disk('local')->assertExists('Laravel/2018-01-01-00-00-00.zip');

        $this->beforeApplicationDestroyed(function () {
            Storage::disk('local')->delete('Laravel/2018-01-01-00-00-00.zip');
        });
    }
}
