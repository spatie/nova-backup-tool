<?php

namespace Spatie\BackupTool\Tests;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Cache;

class BackupStatusesControllerTest extends TestCase
{
    /** @test */
    public function it_can_get_all_backup_statuses()
    {
        $this
            ->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
            ->assertSuccessful()
            ->assertJson([
                [
                    'name' => 'Laravel',
                    'disk' => 'local',
                    'amount' => 0,
                    'newest' => 'No backups present',
                    'usedStorage' => '0 B',
                ],
            ]);
    }

    /** @test */
    public function it_caches_the_index_of_a_disk()
    {
        Cache::shouldReceive('remember')
            ->with('backup-statuses', Carbon::class, Closure::class)
            ->once();

        $this
            ->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
            ->assertSuccessful();
    }
}
