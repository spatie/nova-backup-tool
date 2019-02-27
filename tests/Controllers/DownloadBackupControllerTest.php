<?php

namespace Spatie\BackupTool\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadBackupControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2018)->startOfYear());

        $this->createBackup();
    }

    /** @test */
    public function it_can_download_a_backup()
    {
        $response = $this
            ->getJson('/nova-vendor/spatie/backup-tool/download-backup?disk=local&path=Laravel/2018-01-01-00-00-00.zip')
            ->assertSuccessful()
            ->baseResponse;

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    /** @test */
    public function it_will_not_accept_non_zip_files()
    {
        $this
            ->getJson('/nova-vendor/spatie/backup-tool/download-backup?disk=local&path=Laravel/secret-report.pdf')
            ->assertJsonValidationErrors(['path']);
    }

    public function createBackup()
    {
        $this
            ->postJson('/nova-vendor/spatie/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        Storage::disk('local')->assertExists('Laravel/2018-01-01-00-00-00.zip');
    }
}
