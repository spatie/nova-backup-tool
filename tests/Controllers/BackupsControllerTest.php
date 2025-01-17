<?php

namespace Spatie\BackupTool\Tests\Controllers;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\Attributes\WithMigration;
use Spatie\BackupTool\Jobs\CreateBackupJob;
use Spatie\BackupTool\Tests\TestCase;
use Workbench\Database\Factories\UserFactory;

use function Orchestra\Testbench\remote;

#[WithMigration('laravel', 'cache', 'queue')]
class BackupsControllerTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        Carbon::setTestNow(Carbon::parse('2018-01-01')->startOfDay());

        parent::setUp();

        Storage::fake();
    }

    /** @test */
    public function it_returns_no_results_if_no_backups_were_made()
    {
        $this
            ->actingAs(UserFactory::new()->create())
            ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
            ->assertSuccessful()
            ->assertJsonCount(0);
    }

    /** @test */
    public function it_can_create_a_backup()
    {
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
    }

    /** @test */
    public function when_creating_a_backup_it_pushes_the_job_to_a_queue()
    {
        Queue::fake();

        $this
            ->actingAs(UserFactory::new()->create())
            ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        Queue::assertPushed(CreateBackupJob::class);
    }

    /** @test */
    public function it_can_delete_a_backup()
    {
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
    }

    /** @test */
    public function it_wont_delete_backups_for_an_invalid_disk_name()
    {
        $this
            ->actingAs(UserFactory::new()->create())
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
            ->actingAs(UserFactory::new()->create())
            ->getJson('/nova-vendor/spatie/backup-tool/backups?disk='.$disk)
            ->assertSuccessful();
    }
}
