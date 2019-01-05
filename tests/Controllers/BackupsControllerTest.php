<?php

namespace Spatie\BackupTool\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BackupsControllerTest extends TestCase
{
    public function setUp()
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
    public function it_can_delete_a_backup()
    {
        $this
            ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        $pathToZip = 'Laravel/2018-01-01-00-00-00.zip';

        Storage::disk('local')->assertExists($pathToZip);

        $this
            ->deleteJson('/nova-vendor/spatie/backup-tool/backups', [
                'disk' => 'local',
                'path' => 'Laravel/2018-01-01-00-00-00.zip',
            ])
            ->assertSuccessful();

        $pathToZip = 'Laravel/2018-01-01-00-00-00.zip';

        Storage::disk('local')->assertMissing($pathToZip);
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
}
