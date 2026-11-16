<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Events\CleanupWasSuccessful;
use Workbench\Database\Factories\UserFactory;

it('can clean the backups', function () {
    Event::fake([CleanupWasSuccessful::class]);

    Storage::fake('local');
    Storage::disk('local')->put('Laravel/2000-01-01-00-00-00.zip', 'old backup');
    Storage::disk('local')->put('Laravel/2000-01-02-00-00-00.zip', 'newest backup');

    $this
        ->actingAs(UserFactory::new()->create())
        ->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertJsonCount(2);

    $this->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
        ->assertJsonPath('0.amount', 2);

    $this
        ->postJson('/nova-vendor/spatie/backup-tool/clean-backups')
        ->assertNoContent();

    Storage::disk('local')->assertMissing('Laravel/2000-01-01-00-00-00.zip');
    Storage::disk('local')->assertExists('Laravel/2000-01-02-00-00-00.zip');

    $this->getJson('/nova-vendor/spatie/backup-tool/backups?disk=local')
        ->assertJsonCount(1)
        ->assertJsonPath('0.path', 'Laravel/2000-01-02-00-00-00.zip');

    $this->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
        ->assertJsonPath('0.amount', 1);

    Event::assertDispatched(CleanupWasSuccessful::class);
});
