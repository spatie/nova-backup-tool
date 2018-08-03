<?php

namespace Spatie\NovaBackupTool;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaBackupTool extends Tool
{
    public function boot()
    {
        Nova::script('NovaBackupTool', __DIR__.'/../dist/js/tool.js');
        Nova::style('NovaBackupTool', __DIR__.'/../dist/css/tool.css');
    }

    public function renderNavigation()
    {
        return view('NovaBackupTool::navigation');
    }
}
