<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Nova;
use Spatie\Backup\Config\Config;
use Spatie\BackupTool\BackupTool;
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

it('can create a backup and immediately refresh the cached listings', function ($option, $path) {
    Storage::fake('source')->put('source.txt', 'Backup source content');

    config()->set('backup.backup.source.files.include', [Storage::disk('source')->path('source.txt')]);
    config()->set('backup.backup.source.files.relative_path', Storage::disk('source')->path(''));

    app()->forgetInstance(Config::class);

    $this
        ->actingAs(UserFactory::new()->create())
        ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertJsonCount(0);

    $this->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
        ->assertJsonPath('0.amount', 0);

    $this
        ->postJson('/nova-vendor/spatie/backup-tool/backups', ['option' => $option])
        ->assertSuccessful();

    $this->artisan('queue:work', ['--once' => true])->run();

    Storage::disk('local')->assertExists($path);

    $archive = new ZipArchive;
    expect($archive->open(Storage::disk('local')->path($path)))->toBeTrue();
    expect($archive->getFromName('source.txt'))
        ->toBe($option === 'only-db' ? false : 'Backup source content');

    $databaseDump = collect(range(0, $archive->numFiles - 1))
        ->map(fn ($index) => $archive->getNameIndex($index))
        ->first(fn ($name) => str_starts_with($name, 'db-dumps/'));

    expect($databaseDump !== null)->toBe($option !== 'only-files');

    $archive->close();

    $this
        ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertSuccessful()
        ->assertJsonCount(1)
        ->assertJsonPath('0.path', $path);

    $this->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
        ->assertJsonPath('0.amount', 1);

    $this->beforeApplicationDestroyed(function () use ($path) {
        Storage::disk('local')->delete($path);
    });
})->with([
    ['', 'Laravel/2018-01-01-00-00-00.zip'],
    ['only-db', 'Laravel/only-db-2018-01-01-00-00-00.zip'],
    ['only-files', 'Laravel/only-files-2018-01-01-00-00-00.zip'],
]);

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

    $this->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertJsonCount(1);

    $this->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
        ->assertJsonPath('0.amount', 1);

    $this
        ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
            'disk' => 'local',
            'path' => 'Laravel/2018-01-01-00-00-00.zip',
        ])
        ->assertNoContent();

    Storage::disk('local')->assertMissing('Laravel/2018-01-01-00-00-00.zip');

    $this->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertJsonCount(0);

    $this->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
        ->assertJsonPath('0.amount', 0);
});

it('requires a disk when deleting a backup', function () {
    $this
        ->actingAs(UserFactory::new()->create())
        ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
            'path' => 'Laravel/missing.zip',
        ])
        ->assertJsonValidationErrors(['disk']);
});

it('returns a validation error when deleting a missing backup', function () {
    $this
        ->actingAs(UserFactory::new()->create())
        ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
            'disk' => 'local',
            'path' => 'Laravel/missing.zip',
        ])
        ->assertUnprocessable();
});

it('rejects malformed backup options without dispatching a job', function ($option) {
    Queue::fake();

    $this
        ->actingAs(UserFactory::new()->create())
        ->postJson('/nova-vendor/spatie/backup-tool/backups', ['option' => $option])
        ->assertJsonValidationErrors(['option']);

    Queue::assertNothingPushed();
})->with([['unknown'], [['only-files']]]);

it('honors tool authorization for every endpoint', function ($method, $endpoint, $status, $allowed) {
    Queue::fake();

    $this->artisan('nova:publish')->assertSuccessful();

    Nova::serving(function () use ($allowed) {
        Nova::$tools = [(new BackupTool)->canSee(fn () => $allowed)];
    });

    $response = $this
        ->actingAs(UserFactory::new()->create())
        ->{$method}("/nova-vendor/spatie/backup-tool/{$endpoint}");

    if (! $allowed) {
        $response->assertForbidden();

        Queue::assertNothingPushed();

        return;
    }

    $response->assertStatus($status);
})->with([
    ['getJson', 'backups?disk=local', 200],
    ['postJson', 'backups', 200],
    ['deleteJson', 'backups', 422],
    ['getJson', 'download-backup', 422],
    ['getJson', 'backup-statuses', 200],
    ['postJson', 'clean-backups', 204],
])->with([true, false]);

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
