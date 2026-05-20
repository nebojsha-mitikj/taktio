<?php

declare(strict_types=1);

namespace Tests\Feature\Plan;

use App\Models\MonthPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlanAuthorizationTest extends TestCase
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

    private function planWithGoalAndStep(User $user, int $year = 2026, int $month = 5): array
    {
        $plan = MonthPlan::create(['user_id' => $user->id, 'year' => $year, 'month' => $month]);
        $goal = $plan->goals()->create(['title' => 'Goal']);
        $step = $goal->steps()->create(['title' => 'Step']);

        return [$plan, $goal, $step];
    }

    // Main goal

    public function test_main_goal_update_only_affects_authenticated_users_plan(): void
    {
        $owner = $this->user();
        $attacker = $this->user();

        $ownerPlan = MonthPlan::create([
            'user_id' => $owner->id,
            'year' => 2026,
            'month' => 5,
            'main_goal' => 'Owner goal',
        ]);

        $this->actingAs($attacker)
            ->put(route('plan.update', ['year' => 2026, 'month' => '05']), [
                'main_goal' => 'Attacker goal',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('month_plans', [
            'id' => $ownerPlan->id,
            'main_goal' => 'Owner goal',
        ]);

        $this->assertDatabaseHas('month_plans', [
            'user_id' => $attacker->id,
            'year' => 2026,
            'month' => 5,
            'main_goal' => 'Attacker goal',
        ]);
    }

    // Goals

    public function test_user_cannot_update_another_users_goal(): void
    {
        $owner = $this->user();
        $attacker = $this->user();
        [, $goal] = $this->planWithGoalAndStep($owner);

        $this->actingAs($attacker)
            ->put(route('plan.goals.update', $goal), ['title' => 'Hacked'])
            ->assertForbidden();
    }

    public function test_user_cannot_delete_another_users_goal(): void
    {
        $owner = $this->user();
        $attacker = $this->user();
        [, $goal] = $this->planWithGoalAndStep($owner);

        $this->actingAs($attacker)
            ->delete(route('plan.goals.destroy', $goal))
            ->assertForbidden();

        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id]);
    }

    // Steps

    public function test_user_cannot_update_another_users_step(): void
    {
        $owner = $this->user();
        $attacker = $this->user();
        [, $goal, $step] = $this->planWithGoalAndStep($owner);

        $this->actingAs($attacker)
            ->put(route('plan.goals.steps.update', [$goal, $step]), ['title' => 'Hacked'])
            ->assertForbidden();
    }

    public function test_user_cannot_delete_another_users_step(): void
    {
        $owner = $this->user();
        $attacker = $this->user();
        [, $goal, $step] = $this->planWithGoalAndStep($owner);

        $this->actingAs($attacker)
            ->delete(route('plan.goals.steps.destroy', [$goal, $step]))
            ->assertForbidden();

        $this->assertDatabaseHas('plan_goal_steps', ['id' => $step->id]);
    }

    public function test_user_cannot_add_step_to_another_users_goal(): void
    {
        $owner = $this->user();
        $attacker = $this->user();
        [, $goal] = $this->planWithGoalAndStep($owner);

        $this->actingAs($attacker)
            ->post(route('plan.goals.steps.store', $goal), ['title' => 'Injected'])
            ->assertForbidden();

        $this->assertDatabaseCount('plan_goal_steps', 1);
    }

    // Nested route mismatch

    public function test_user_cannot_update_step_through_wrong_goal_route(): void
    {
        $user = $this->user();
        $plan = MonthPlan::create(['user_id' => $user->id, 'year' => 2026, 'month' => 5]);
        $goalA = $plan->goals()->create(['title' => 'Goal A']);
        $goalB = $plan->goals()->create(['title' => 'Goal B']);
        $stepB = $goalB->steps()->create(['title' => 'Step B']);

        $this->actingAs($user)
            ->put(route('plan.goals.steps.update', [$goalA, $stepB]), ['title' => 'Wrong route'])
            ->assertNotFound();
    }

    public function test_user_cannot_delete_step_through_wrong_goal_route(): void
    {
        $user = $this->user();
        $plan = MonthPlan::create(['user_id' => $user->id, 'year' => 2026, 'month' => 5]);
        $goalA = $plan->goals()->create(['title' => 'Goal A']);
        $goalB = $plan->goals()->create(['title' => 'Goal B']);
        $stepB = $goalB->steps()->create(['title' => 'Step B']);

        $this->actingAs($user)
            ->delete(route('plan.goals.steps.destroy', [$goalA, $stepB]))
            ->assertNotFound();

        $this->assertDatabaseHas('plan_goal_steps', ['id' => $stepB->id]);
    }
}
