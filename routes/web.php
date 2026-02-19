<?php

use Illuminate\Support\Facades\Route;
use AbinashBhatta\QueueInspector\Http\Controllers\DashboardController;
use AbinashBhatta\QueueInspector\Http\Controllers\JobController;
use AbinashBhatta\QueueInspector\Http\Middleware\QueueInspectorAuth;

Route::group([
    'prefix'     => config('queue-inspector.path'),
    'middleware' => ['web', QueueInspectorAuth::class],
    'as'         => 'queue-inspector.',
], function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/pending', [JobController::class, 'pending'])
        ->name('jobs.pending');
    Route::get('/failed', [JobController::class, 'failed'])
        ->name('jobs.failed');
    Route::post('/failed/{id}/retry', [JobController::class, 'retry'])
        ->name('jobs.retry');
    Route::delete('/failed/{id}', [JobController::class, 'destroy'])
        ->name('jobs.destroy');
    Route::post('/failed/retry-all', [JobController::class, 'retryAll'])
        ->name('jobs.retry-all');
    Route::delete('/failed/clear-all', [JobController::class, 'clearAll'])
        ->name('jobs.clear-all');
    Route::get('/success', [JobController::class, 'success'])
        ->name('jobs.success');
});
