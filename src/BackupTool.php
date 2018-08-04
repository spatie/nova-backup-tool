<?php

namespace Spatie\BackupTool;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class BackupTool extends Tool
{
    public function boot()
    {
        Nova::script('BackupTool', __DIR__.'/../dist/js/tool.js');
        Nova::style('BackupTool', __DIR__.'/../dist/css/tool.css');
    }

    public function renderNavigation()
    {
        return view('BackupTool::navigation');
    }
}
