<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], static function () {
    Route::get('/today', [TaskController::class, 'today'])->name('tasks.today');
    Route::get('/upcoming', [TaskController::class, 'upcoming'])->name('tasks.upcoming');
    Route::get('/history', [TaskController::class, 'history'])->name('tasks.history');

    Route::put('/tasks/{task}/completed', [TaskController::class, 'updateCompleted'])
        ->name('tasks.completed.update')
        ->middleware('can:update,task');

    Route::put('/tasks/{task}/labels', [TaskController::class, 'updateLabels'])
        ->name('tasks.labels.update')
        ->middleware('can:update,task');

    Route::post('/tasks/{template}/materialize', [TaskController::class, 'materialize'])
        ->name('tasks.materialize')
        ->middleware('can:materialize,template');

    Route::resource('tasks', TaskController::class)->only(['destroy', 'update', 'store'])
        ->middlewareFor('update', 'can:update,task')
        ->middlewareFor('destroy', 'can:delete,task');
});
