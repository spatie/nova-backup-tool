<?php

use Illuminate\Support\Facades\Route;
use Spatie\BackupTool\Controllers\BackupsController;
use Spatie\BackupTool\Controllers\BackupStatussesController;
use Spatie\BackupTool\Controllers\CleanBackupsController;
use Spatie\BackupTool\Controllers\DownloadBackupController;

Route::get('backups', BackupsController::class . '@index');
Route::post('backups', BackupsController::class . '@create');
Route::delete('backups', BackupsController::class . '@delete');

Route::get('download-backup', DownloadBackupController::class);

Route::get('backup-statusses', BackupStatussesController::class . '@index');
Route::post('clean-backups', CleanBackupsController::class);