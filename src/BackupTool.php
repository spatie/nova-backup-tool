<?php

namespace Spatie\BackupTool;

use Closure;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Symfony\Component\HttpFoundation\Request;

class BackupTool extends Tool
{
    public function boot()
    {
        Nova::script('BackupTool', __DIR__.'/../dist/js/tool.js');
    }

    public function renderNavigation()
    {
        return view('BackupTool::navigation');
    }
}
