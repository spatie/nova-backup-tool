<?php

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Workbench\Database\Factories\UserFactory;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2018-01-01')->startOfDay());

    $this->createBackup();
});

it('can download a backup', function () {
    $response = $this
        ->actingAs(UserFactory::new()->create())
        ->getJson('/nova-vendor/spatie/backup-tool/download-backup?disk=local&path=Laravel/2018-01-01-00-00-00.zip')
        ->assertSuccessful()
        ->baseResponse;

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

it('will not accept non zip files', function () {
    $this
        ->actingAs(UserFactory::new()->create())
        ->getJson('/nova-vendor/spatie/backup-tool/download-backup?disk=local&path=Laravel/secret-report.pdf')
        ->assertJsonValidationErrors(['path']);
});
