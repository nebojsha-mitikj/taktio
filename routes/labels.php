<?php

use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('labels', LabelController::class)
        ->except(['create', 'edit'])
        ->middlewareFor('show', 'can:view,label')
        ->middlewareFor('update', 'can:update,label')
        ->middlewareFor('destroy', 'can:delete,label');
});
