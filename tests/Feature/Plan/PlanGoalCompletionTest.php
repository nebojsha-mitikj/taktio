<?php

declare(strict_types=1);

namespace Tests\Feature\Plan;

use App\Models\MonthPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlanGoalCompletionTest extends TestCase
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

    private function currentPlan(User $user): MonthPlan
    {
        return MonthPlan::create(['user_id' => $user->id, 'year' => 2026, 'month' => 5]);
    }

    public function test_completing_goal_marks_all_steps_complete(): void
    {
        $user = $this->user();
        $plan = $this->currentPlan($user);
        $goal = $plan->goals()->create(['title' => 'Goal']);
        $step1 = $goal->steps()->create(['title' => 'Step 1']);
        $step2 = $goal->steps()->create(['title' => 'Step 2']);

        $this->actingAs($user)
            ->put(route('plan.goals.update', $goal), ['completed' => true])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_goal_steps', ['id' => $step1->id, 'completed' => true]);
        $this->assertDatabaseHas('plan_goal_steps', ['id' => $step2->id, 'completed' => true]);
        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id, 'completed' => true]);
    }

    public function test_uncompleting_goal_does_not_change_steps(): void
    {
        $user = $this->user();
        $plan = $this->currentPlan($user);
        $goal = $plan->goals()->create(['title' => 'Goal', 'completed' => true]);
        $step1 = $goal->steps()->create(['title' => 'Step 1', 'completed' => true]);
        $step2 = $goal->steps()->create(['title' => 'Step 2', 'completed' => true]);

        $this->actingAs($user)
            ->put(route('plan.goals.update', $goal), ['completed' => false])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_goal_steps', ['id' => $step1->id, 'completed' => true]);
        $this->assertDatabaseHas('plan_goal_steps', ['id' => $step2->id, 'completed' => true]);
        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id, 'completed' => false]);
    }

    public function test_completing_all_steps_marks_goal_complete(): void
    {
        $user = $this->user();
        $plan = $this->currentPlan($user);
        $goal = $plan->goals()->create(['title' => 'Goal']);
        $step1 = $goal->steps()->create(['title' => 'Step 1', 'completed' => true]);
        $step2 = $goal->steps()->create(['title' => 'Step 2']);

        $this->actingAs($user)
            ->put(route('plan.goals.steps.update', [$goal, $step2]), ['completed' => true])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id, 'completed' => true]);
    }

    public function test_unchecking_one_step_makes_parent_goal_incomplete(): void
    {
        $user = $this->user();
        $plan = $this->currentPlan($user);
        $goal = $plan->goals()->create(['title' => 'Goal', 'completed' => true]);
        $step1 = $goal->steps()->create(['title' => 'Step 1', 'completed' => true]);
        $step2 = $goal->steps()->create(['title' => 'Step 2', 'completed' => true]);

        $this->actingAs($user)
            ->put(route('plan.goals.steps.update', [$goal, $step1]), ['completed' => false])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id, 'completed' => false]);
        $this->assertDatabaseHas('plan_goal_steps', ['id' => $step2->id, 'completed' => true]);
    }

    public function test_goal_with_no_steps_stays_as_set_when_completing(): void
    {
        $user = $this->user();
        $plan = $this->currentPlan($user);
        $goal = $plan->goals()->create(['title' => 'Goal']);

        $this->actingAs($user)
            ->put(route('plan.goals.update', $goal), ['completed' => true])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id, 'completed' => true]);
    }

    public function test_deleting_only_incomplete_step_marks_parent_goal_complete(): void
    {
        $user = $this->user();
        $plan = $this->currentPlan($user);
        $goal = $plan->goals()->create(['title' => 'Goal', 'completed' => false]);
        $goal->steps()->create(['title' => 'Done step', 'completed' => true]);
        $incomplete = $goal->steps()->create(['title' => 'Incomplete step', 'completed' => false]);

        $this->actingAs($user)
            ->delete(route('plan.goals.steps.destroy', [$goal, $incomplete]))
            ->assertRedirect();

        $this->assertDatabaseHas('plan_goals', ['id' => $goal->id, 'completed' => true]);
    }
}
