<?php

namespace Spatie\BackupTool;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class BackupTool extends Tool
{
    public function boot()
    {
        Nova::script('BackupTool', __DIR__.'/../dist/js/tool.js');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuSection::make('Backups')
            ->path('/backups')
            ->icon('server');
    }
}
