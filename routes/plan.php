<?php

declare(strict_types=1);

use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanGoalController;
use App\Http\Controllers\PlanGoalStepController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], static function () {
    Route::get('/plan', [PlanController::class, 'index'])->name('plan.index');

    // Specific non-parameterized sub-routes must come before /{year}/{month} to avoid capture
    Route::put('/plan/goals/{goal}', [PlanGoalController::class, 'update'])
        ->middleware('can:update,goal')
        ->name('plan.goals.update');

    Route::delete('/plan/goals/{goal}', [PlanGoalController::class, 'destroy'])
        ->middleware('can:delete,goal')
        ->name('plan.goals.destroy');

    Route::post('/plan/goals/{goal}/steps', [PlanGoalStepController::class, 'store'])
        ->middleware('can:update,goal')
        ->name('plan.goals.steps.store');

    Route::put('/plan/goals/{goal}/steps/{step}', [PlanGoalStepController::class, 'update'])
        ->middleware('can:update,step')
        ->name('plan.goals.steps.update');

    Route::delete('/plan/goals/{goal}/steps/{step}', [PlanGoalStepController::class, 'destroy'])
        ->middleware('can:delete,step')
        ->name('plan.goals.steps.destroy');

    Route::get('/plan/{year}/{month}', [PlanController::class, 'show'])
        ->name('plan.show')
        ->whereNumber(['year', 'month']);

    Route::put('/plan/{year}/{month}', [PlanController::class, 'update'])
        ->name('plan.update')
        ->whereNumber(['year', 'month']);

    Route::post('/plan/{year}/{month}/goals', [PlanGoalController::class, 'store'])
        ->name('plan.goals.store')
        ->whereNumber(['year', 'month']);

});
