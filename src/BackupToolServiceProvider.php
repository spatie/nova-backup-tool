<?php

namespace Spatie\BackupTool;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\BackupTool\Http\Middleware\Authorize;

class BackupToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/novabackuptool.php' => config_path('novabackuptool.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/nova-backup-tool'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'BackupTool');

        $this->registerTranslations();

        $this->app->booted(function () {
            $this->routes();
        });

        $this->provideConfigToScript();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/novabackuptool.php', 'novabackuptool');
    }

    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('/nova-vendor/spatie/backup-tool')
            ->group(
                __DIR__.'/../routes/api.php'
            );
    }

    protected function provideConfigToScript()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::provideToScript([
                'novabackuptool' => [
                    'polling' => config('novabackuptool.polling'),
                    'polling_interval' => config('novabackuptool.polling_interval'),
                ]
            ]);
        });
    }

    protected function registerTranslations()
    {
        $currentLocale = app()->getLocale();

        Nova::translations(__DIR__.'/../resources/lang/'.$currentLocale.'.json');
        Nova::translations(resource_path('lang/vendor/nova-backup-tool/'.$currentLocale.'.json'));

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'BackupTool');
        $this->loadJSONTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadJSONTranslationsFrom(resource_path('lang/vendor/nova-backup-tool'));
    }
}
