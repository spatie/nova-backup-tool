<?php

namespace Spatie\BackupTool\Tests\Middleware;

use Spatie\BackupTool\BackupTool;
use Spatie\BackupTool\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTest extends TestCase
{
    /** @test */
    public function it_will_deny_access_if_the_auth_function_returns_false()
    {
        $this->get('/nova/backup-tool/backup-statusses')->assertSuccessful();

        BackupTool::auth(function() {
            return false;
        });

        $this->get('/nova/backup-tool/backup-statusses')->assertStatus(Response::HTTP_FORBIDDEN);

    }
}