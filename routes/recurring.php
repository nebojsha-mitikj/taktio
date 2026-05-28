<?php

use App\Http\Controllers\RecurringTaskTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], static function () {
    Route::get('/recurring', [RecurringTaskTemplateController::class, 'recurring'])
        ->name('recurring.index');

    Route::put('/recurring/{template}/labels', [RecurringTaskTemplateController::class, 'updateLabels'])
        ->middleware('can:update,template')
        ->name('recurring.labels.update');

    Route::put('/recurring/{template}/toggle', [RecurringTaskTemplateController::class, 'toggle'])
        ->middleware('can:update,template')
        ->name('recurring.toggle');

    Route::resource('recurring', RecurringTaskTemplateController::class)
        ->only(['destroy', 'update', 'store'])
        ->parameters(['recurring' => 'template'])
        ->middlewareFor('update', 'can:update,template')
        ->middlewareFor('destroy', 'can:delete,template');
});
