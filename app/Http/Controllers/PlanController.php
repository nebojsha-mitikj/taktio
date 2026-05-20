<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlanRequest;
use App\Models\MonthPlan;
use App\Traits\ResolvesPlanDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    use ResolvesPlanDate;

    public function index(): RedirectResponse
    {
        $now = Carbon::today();

        return redirect()->route('plan.show', [
            'year' => $now->year,
            'month' => $now->format('m'),
        ]);
    }

    public function show(int $year, int $month): Response
    {
        $this->abortInvalidDate($year, $month);
        $mode = $this->resolveMode($year, $month);

        if ($mode === self::MODE_PAST) {
            $plan = MonthPlan::query()
                ->with('goals.steps')
                ->where('user_id', auth()->id())
                ->where('year', $year)
                ->where('month', $month)
                ->first();
        } else {
            $plan = MonthPlan::firstOrCreate([
                'user_id' => auth()->id(),
                'year' => $year,
                'month' => $month,
            ]);
            $plan->load('goals.steps');
        }

        return Inertia::render('plan/Plan', [
            'plan' => $plan,
            'year' => $year,
            'month' => $month,
            'mode' => $mode,
        ]);
    }

    public function update(UpdatePlanRequest $request, int $year, int $month): RedirectResponse
    {
        $this->abortInvalidDate($year, $month);

        abort_if($this->resolveMode($year, $month) === self::MODE_PAST, 403);

        $plan = MonthPlan::firstOrCreate([
            'user_id' => auth()->id(),
            'year' => $year,
            'month' => $month,
        ]);
        $plan->update(['main_goal' => $request->main_goal]);

        return back()->with('success', 'Main goal updated.');
    }
}
