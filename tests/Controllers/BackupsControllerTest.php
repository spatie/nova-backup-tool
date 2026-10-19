<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\BackupTool\Jobs\CreateBackupJob;
use Workbench\Database\Factories\UserFactory;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2018-01-01')->startOfDay());

    Storage::fake();
});

it('returns no results if no backups were made', function () {
    $this
        ->actingAs(UserFactory::new()->create())
        ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertSuccessful()
        ->assertJsonCount(0);
});

it('can create a backup', function () {
    $this
        ->actingAs(UserFactory::new()->create())
        ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
        ->assertSuccessful();

    $this->artisan('queue:work', ['--once' => true])->run();

    Storage::disk('local')->assertExists('Laravel/2018-01-01-00-00-00.zip');

    $this
        ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertSuccessful()
        ->assertJsonCount(1)
        ->assertJsonStructure([0 => ['path', 'date', 'size']]);

    $this->beforeApplicationDestroyed(function () {
        Storage::disk('local')->delete('Laravel/2018-01-01-00-00-00.zip');
    });
});

it('pushes the backup job to a queue', function () {
    Queue::fake();

    $this
        ->actingAs(UserFactory::new()->create())
        ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
        ->assertSuccessful();

    Queue::assertPushed(CreateBackupJob::class);
});

it('can delete a backup', function () {
    $this
        ->actingAs(UserFactory::new()->create())
        ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
        ->assertSuccessful();

    $this->artisan('queue:work', ['--once' => true])->run();

    Storage::disk('local')->assertExists('Laravel/2018-01-01-00-00-00.zip');

    $this
        ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
            'disk' => 'local',
            'path' => 'Laravel/2018-01-01-00-00-00.zip',
        ])
        ->assertSuccessful();

    Storage::disk('local')->assertMissing('Laravel/2018-01-01-00-00-00.zip');
});

it('will not delete backups for an invalid disk name', function () {
    $this
        ->actingAs(UserFactory::new()->create())
        ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
            'disk' => 'does-not-exist',
            'file' => 'Laravel/2018-01-01-00-00-00.zip',
        ])
        ->assertJsonValidationErrors(['disk']);
});

it('caches the index of a disk', function () {
    $disk = 'local';

    Cache::shouldReceive('remember')
        ->with('backups-'.$disk, Carbon::class, Closure::class)
        ->once();

    $this
        ->actingAs(UserFactory::new()->create())
        ->getJson('/nova-vendor/spatie/backup-tool/backups?disk='.$disk)
        ->assertSuccessful();
});
