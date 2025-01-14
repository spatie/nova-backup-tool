<?php

namespace Spatie\BackupTool\Tests\Controllers;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\Attributes\WithMigration;
use Spatie\BackupTool\Tests\TestCase;
use Workbench\Database\Factories\UserFactory;

#[WithMigration('laravel', 'cache', 'queue')]
class BackupStatusesControllerTest extends TestCase
{
    /** @test */
    public function it_can_get_all_backup_statuses()
    {
        $this
            ->actingAs(UserFactory::new()->create())
            ->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
            ->assertSuccessful()
            ->assertJson([
                [
                    'name' => 'Laravel',
                    'disk' => 'local',
                    'amount' => 0,
                    'newest' => 'No backups present',
                    'usedStorage' => '0 KB',
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
            ->actingAs(UserFactory::new()->create())
            ->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
            ->assertSuccessful();
    }
}
