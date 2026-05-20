<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanGoalRequest;
use App\Http\Requests\UpdatePlanGoalRequest;
use App\Models\MonthPlan;
use App\Models\PlanGoal;
use App\Traits\ResolvesPlanDate;
use Illuminate\Http\RedirectResponse;

class PlanGoalController extends Controller
{
    use ResolvesPlanDate;

    public function store(StorePlanGoalRequest $request, int $year, int $month): RedirectResponse
    {
        $this->abortInvalidDate($year, $month);
        abort_if($this->isPast($year, $month), 403);
        $plan = MonthPlan::firstOrCreate([
            'user_id' => auth()->id(),
            'year' => $year,
            'month' => $month,
        ]);
        $plan->goals()->create([
            'title' => $request->title,
        ]);

        return back()->with('success', 'Goal added.');
    }

    public function update(UpdatePlanGoalRequest $request, PlanGoal $goal): RedirectResponse
    {
        abort_if($this->isPast($goal->monthPlan->year, $goal->monthPlan->month), 403);
        if ($request->validated('completed') === true) {
            $goal->steps()->update(['completed' => true]);
        }
        $goal->update($request->validated());

        return back();
    }

    public function destroy(PlanGoal $goal): RedirectResponse
    {
        abort_if($this->isPast($goal->monthPlan->year, $goal->monthPlan->month), 403);
        $goal->delete();

        return back()->with('success', 'Goal deleted.');
    }
}
