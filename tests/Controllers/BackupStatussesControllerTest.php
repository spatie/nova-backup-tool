<?php

namespace Spatie\BackupTool\Tests;

class BackupStatussesControllerTest extends TestCase
{
    /** @test */
    public function it_can_get_all_backup_statusses()
    {
        $this
            ->getJson('/nova-vendor/spatie/backup-tool/backup-statusses')
            ->assertSuccessful()
            ->assertJson([
                [
                    'name' => 'Laravel',
                    'disk' => 'local',
                    'amount' => 0,
                    'newest' => 'No backups present',
                    'usedStorage' => '0 KB',
                ]
            ]);
    }
}