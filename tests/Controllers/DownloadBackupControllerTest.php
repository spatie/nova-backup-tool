<?php

namespace Spatie\BackupTool\Tests\Controllers;

use Carbon\Carbon;
use Orchestra\Testbench\Attributes\WithMigration;
use Spatie\BackupTool\Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Workbench\Database\Factories\UserFactory;

#[WithMigration('laravel', 'cache', 'queue')]
class DownloadBackupControllerTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2018-01-01')->startOfDay());

        $this->createBackup();
    }

    /** @test */
    public function it_can_download_a_backup()
    {
        $response = $this
            ->actingAs(UserFactory::new()->create())
            ->getJson('/nova-vendor/spatie/backup-tool/download-backup?disk=local&path=Laravel/2018-01-01-00-00-00.zip')
            ->assertSuccessful()
            ->baseResponse;

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    /** @test */
    public function it_will_not_accept_non_zip_files()
    {
        $this
            ->actingAs(UserFactory::new()->create())
            ->getJson('/nova-vendor/spatie/backup-tool/download-backup?disk=local&path=Laravel/secret-report.pdf')
            ->assertJsonValidationErrors(['path']);
    }
}
