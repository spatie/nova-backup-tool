<?php

namespace Spatie\BackupTool\Tests\Controllers;

use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\Attributes\WithMigration;
use Spatie\BackupTool\Tests\TestCase;
use Spatie\Backup\Events\CleanupWasSuccessful;
use Workbench\Database\Factories\UserFactory;

#[WithMigration('laravel', 'cache', 'queue')]
class CleanBackupsControllerTest extends TestCase
{
    /** @test */
    public function it_can_clean_the_backups()
    {
        Event::fake();

        $this
            ->actingAs(UserFactory::new()->create())
            ->postJson('/nova-vendor/spatie/backup-tool/clean-backups')
            ->assertSuccessful();

        Event::assertDispatched(CleanupWasSuccessful::class);
    }
}
