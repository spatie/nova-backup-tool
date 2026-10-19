<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Workbench\Database\Factories\UserFactory;

it('can get all backup statuses', function () {
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
});

it('caches the backup statuses', function () {
    Cache::shouldReceive('remember')
        ->with('backup-statuses', Carbon::class, Closure::class)
        ->once();

    $this
        ->actingAs(UserFactory::new()->create())
        ->getJson('/nova-vendor/spatie/backup-tool/backup-statuses')
        ->assertSuccessful();
});
