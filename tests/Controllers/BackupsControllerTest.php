<?php

namespace Spatie\BackupTool\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BackupsControllerTest extends TestCase
{
    /** @test */
    public function it_returns_no_results_if_no_backups_were_made()
    {
        $this
            ->getJson('/nova/backup-tool/backups?disk=local')
            ->assertSuccessful()
            ->assertJsonCount(0);
    }

    /** @test */
    public function it_can_create_a_backup()
    {
        Carbon::setTestNow(Carbon::create(2018)->startOfYear());

        $this->withoutExceptionHandling();

        $this
            ->postJson('/nova/backup-tool/backups', ['disk' => 'local'])
            ->assertSuccessful();

        Storage::disk('local')->assertExists('Laravel/2018-01-01-00-00-00.zip');

        $this
            ->getJson('/nova/backup-tool/backups?disk=local')
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJsonStructure([0 => ['path', 'date', 'size']]);
    }
}