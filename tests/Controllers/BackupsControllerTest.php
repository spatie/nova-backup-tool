<?php

namespace Spatie\BackupTool\Tests;

class BackupsControllerTest extends TestCase
{
    /** @test */
    public function it_returns_no_results_if_no_backups_were_made()
    {
        $this->withoutExceptionHandling();

        $this
            ->getJson('/nova/backup-tool/backups?disk=local')
            ->assertSuccessful();
    }
}