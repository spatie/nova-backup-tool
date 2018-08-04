<?php

namespace Spatie\NovaBackupTool;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;
use Spatie\NovaBackupTool\Controllers\BackupsController;
use Spatie\NovaBackupTool\Controllers\BackupStatussesController;
use Spatie\NovaBackupTool\Controllers\CleanBackupsController;

class NovaBackupToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'NovaBackupTool');
    }

    public function register()
    {
        Route::prefix($this->getNovaUrl('backup-tool'))->group(function () {
            Route::get('backups', BackupsController::class . '@index');
            Route::post('backups', BackupsController::class . '@create');
            Route::delete('backups', BackupsController::class . '@delete');

            Route::get('backup-statusses', BackupStatussesController::class . '@index');
            Route::post('clean-backups', CleanBackupsController::class);
        });
    }

    public function getNovaUrl(string $url = '/'): string
    {
        if (!class_exists(Nova::class)) {
            return "/nova/{$url}";
        }

        return Nova::path() . "/{$url}";
    }
}
