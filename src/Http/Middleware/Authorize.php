<?php

namespace Spatie\BackupTool\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Spatie\BackupTool\BackupTool;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            collect(Nova::availableTools($request))->contains(fn ($tool) => $tool instanceof BackupTool),
            403,
        );

        return $next($request);
    }
}
