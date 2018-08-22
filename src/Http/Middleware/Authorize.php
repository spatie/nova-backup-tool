<?php

namespace Spatie\BackupTool\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\BackupTool\BackupTool;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    public function handle(Request $request, Closure $next): Response
    {
        return app(BackupTool::class)->authorize($request)
            ? $next($request)
            : abort(403);
    }
}
