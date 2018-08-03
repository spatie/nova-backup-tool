<?php

namespace Spatie\NovaBackupTool;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;
use Spatie\NovaBackupTool\Controllers\BackupController;

class NovaBackupToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'NovaBackupTool');
    }

    public function register()
    {
        $router = $this->app['router'];

        $router->get($this->getNovaUrl('backup-tool'), BackupController::class . '@index');
    }

    public function getNovaUrl(string $url = '/'): string
    {
        if (! class_exists(Nova::class)) {
            return "/nova/{$url}";
        }

        return Nova::path() . "/{$url}";
    }
}
