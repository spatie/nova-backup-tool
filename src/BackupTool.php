<?php

namespace Spatie\BackupTool;

use Closure;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Symfony\Component\HttpFoundation\Request;

class BackupTool extends Tool
{
    /** @var \Closure */
    public static $authUsing;

    public function boot()
    {
        Nova::script('BackupTool', __DIR__.'/../dist/js/tool.js');
        Nova::style('BackupTool', __DIR__.'/../dist/css/tool.css');

        $this->canSee(function () {
            return static::check(request());
        });
    }

    public function renderNavigation()
    {
        return view('BackupTool::navigation');
    }

    public static function auth(Closure $callback): BackupTool
    {
        static::$authUsing = $callback;

        return new static;
    }

    public static function check(Request $request): bool
    {
        return (static::$authUsing ?? function () {
                return app()->environment('local');
            })($request);
    }
}
