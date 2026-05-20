<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanGoalStepRequest;
use App\Http\Requests\UpdatePlanGoalStepRequest;
use App\Models\PlanGoal;
use App\Models\PlanGoalStep;
use App\Traits\ResolvesPlanDate;
use Illuminate\Http\RedirectResponse;

class PlanGoalStepController extends Controller
{
    use ResolvesPlanDate;

    public function store(StorePlanGoalStepRequest $request, PlanGoal $goal): RedirectResponse
    {
        abort_if($this->isPast($goal->monthPlan->year, $goal->monthPlan->month), 403);
        $goal->steps()->create($request->validated());

        return back()->with('success', 'Step added.');
    }

    public function update(UpdatePlanGoalStepRequest $request, PlanGoal $goal, PlanGoalStep $step): RedirectResponse
    {
        abort_if($step->plan_goal_id !== $goal->id, 404);
        abort_if($this->isPast($goal->monthPlan->year, $goal->monthPlan->month), 403);
        $step->update($request->validated());
        if ($request->has('completed')) {
            $this->syncParentGoal($goal->fresh());
        }

        return back();
    }

    public function destroy(PlanGoal $goal, PlanGoalStep $step): RedirectResponse
    {
        abort_if($step->plan_goal_id !== $goal->id, 404);
        abort_if($this->isPast($goal->monthPlan->year, $goal->monthPlan->month), 403);
        $step->delete();
        $this->syncParentGoal($goal->fresh());

        return back()->with('success', 'Step deleted.');
    }

    private function syncParentGoal(PlanGoal $goal): void
    {
        $steps = $goal->steps;
        if ($steps->isEmpty()) {
            return;
        }
        $allComplete = $steps->every(fn (PlanGoalStep $s) => $s->completed);
        if ($allComplete && ! $goal->completed) {
            $goal->update(['completed' => true]);
        } elseif (! $allComplete && $goal->completed) {
            $goal->update(['completed' => false]);
        }
    }
}
