<?php

namespace Spatie\NovaBackupTool\Tests;

class BackupsControllerTest extends TestCase
{
    /** @test */
    public function it_tests()
    {
        $this->withoutExceptionHandling();

        $this
            ->getJson('/nova/backup-tool/backups')
            ->assertSuccessful();
    }
}