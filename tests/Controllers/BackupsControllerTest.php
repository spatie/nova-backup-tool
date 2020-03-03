<?php

namespace Spatie\BackupTool\Tests;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\BackupTool\Jobs\CreateBackupJob;

class BackupsControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2018)->startOfYear());
    }

    /** @test */
    public function it_returns_no_results_if_no_backups_were_made()
    {
        $this
            ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
            ->assertSuccessful()
            ->assertJsonCount(0);
    }

    /** @test */
    public function it_can_create_a_backup()
    {
        $this
            ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        Storage::disk('local')->assertExists('Laravel/2018-01-01-00-00-00.zip');

        $this
            ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJsonStructure([0 => ['path', 'date', 'size']]);
    }

    /** @test */
    public function when_creating_a_backup_it_pushes_the_job_to_a_queue()
    {
        Queue::fake();

        $this
            ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        Queue::assertPushed(CreateBackupJob::class);
    }

    /** @test */
    public function it_can_delete_a_backup()
    {
        $this
            ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        Storage::disk('local')->assertExists('Laravel/2018-01-01-00-00-00.zip');

        $this
            ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
                'disk' => 'local',
                'path' => 'Laravel/2018-01-01-00-00-00.zip',
            ])
            ->assertSuccessful();

        Storage::disk('local')->assertMissing('Laravel/2018-01-01-00-00-00.zip');
    }

    /** @test */
    public function it_wont_delete_backups_for_an_invalid_disk_name()
    {
        $this
            ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
                'disk' => 'does-not-exist',
                'file' => 'Laravel/2018-01-01-00-00-00.zip',
            ])
            ->assertJsonValidationErrors([
                'disk',
            ]);
    }

    /** @test */
    public function it_caches_the_index_of_a_disk()
    {
        $disk = 'local';

        Cache::shouldReceive('remember')
            ->with('backups-'.$disk, Carbon::class, Closure::class)
            ->once();

        $this
            ->getJson('/nova-vendor/spatie/backup-tool/backups?disk='.$disk)
            ->assertSuccessful();
    }
}
