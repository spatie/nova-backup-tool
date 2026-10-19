<?php

use Illuminate\Support\Facades\Event;
use Spatie\Backup\Events\CleanupWasSuccessful;
use Workbench\Database\Factories\UserFactory;

it('can clean the backups', function () {
    Event::fake();

    $this
        ->actingAs(UserFactory::new()->create())
        ->postJson('/nova-vendor/spatie/backup-tool/clean-backups')
        ->assertSuccessful();

    Event::assertDispatched(CleanupWasSuccessful::class);
});
