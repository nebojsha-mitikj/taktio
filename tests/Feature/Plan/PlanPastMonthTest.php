<?php

declare(strict_types=1);

namespace Tests\Feature\Plan;

use App\Models\MonthPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlanPastMonthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-05-06 12:00:00', 'Europe/Skopje'));
    }

    private function user(): User
    {
        return User::factory()->withoutTwoFactor()->create();
    }

    private function pastPlanWithGoalAndStep(User $user): array
    {
        $plan = MonthPlan::create(['user_id' => $user->id, 'year' => 2026, 'month' => 4]);
        $goal = $plan->goals()->create(['title' => 'Old Goal']);
        $step = $goal->steps()->create(['title' => 'Old Step']);

        return [$plan, $goal, $step];
    }

    public function test_past_month_plan_is_read_only_in_show(): void
    {
        $user = $this->user();
        [$plan] = $this->pastPlanWithGoalAndStep($user);

        $this->actingAs($user)
            ->get(route('plan.show', ['year' => 2026, 'month' => '04']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('mode', 'past'));
    }

    public function test_past_month_show_does_not_create_empty_plan(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get(route('plan.show', ['year' => 2026, 'month' => '04']))
            ->assertOk();

        $this->assertDatabaseMissing('month_plans', ['user_id' => $user->id, 'year' => 2026, 'month' => 4]);
    }

    public function test_cannot_update_main_goal_for_past_month(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('plan.update', ['year' => 2026, 'month' => '04']), ['main_goal' => 'New'])
            ->assertForbidden();
    }

    public function test_cannot_store_goal_for_past_month(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->post(route('plan.goals.store', ['year' => 2026, 'month' => '04']), ['title' => 'New Goal'])
            ->assertForbidden();
    }

    public function test_cannot_update_goal_in_past_month(): void
    {
        $user = $this->user();
        [, $goal] = $this->pastPlanWithGoalAndStep($user);

        $this->actingAs($user)
            ->put(route('plan.goals.update', $goal), ['title' => 'Changed'])
            ->assertForbidden();
    }

    public function test_cannot_delete_goal_in_past_month(): void
    {
        $user = $this->user();
        [, $goal] = $this->pastPlanWithGoalAndStep($user);

        $this->actingAs($user)
            ->delete(route('plan.goals.destroy', $goal))
            ->assertForbidden();

        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id]);
    }

    public function test_cannot_store_step_for_goal_in_past_month(): void
    {
        $user = $this->user();
        [, $goal] = $this->pastPlanWithGoalAndStep($user);

        $this->actingAs($user)
            ->post(route('plan.goals.steps.store', $goal), ['title' => 'New Step'])
            ->assertForbidden();
    }

    public function test_cannot_update_step_in_past_month(): void
    {
        $user = $this->user();
        [, $goal, $step] = $this->pastPlanWithGoalAndStep($user);

        $this->actingAs($user)
            ->put(route('plan.goals.steps.update', [$goal, $step]), ['title' => 'Changed'])
            ->assertForbidden();
    }

    public function test_cannot_delete_step_in_past_month(): void
    {
        $user = $this->user();
        [, $goal, $step] = $this->pastPlanWithGoalAndStep($user);

        $this->actingAs($user)
            ->delete(route('plan.goals.steps.destroy', [$goal, $step]))
            ->assertForbidden();

        $this->assertDatabaseHas('plan_goal_steps', ['id' => $step->id]);
    }
}
