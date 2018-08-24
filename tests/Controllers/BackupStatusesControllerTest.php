<?php

namespace Spatie\BackupTool\Tests;

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
                    'usedStorage' => '0 KB',
                ],
            ]);
    }
}
