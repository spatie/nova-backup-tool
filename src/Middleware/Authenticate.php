<?php

namespace Spatie\BackupTool\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\BackupTool\BackupTool;
use Spatie\NovaTailTool\NovaTailTool;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        return BackupTool::check($request)
            ? $next($request)
            : abort(403);
    }
}