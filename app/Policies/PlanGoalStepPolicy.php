<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PlanGoalStep;
use App\Models\User;

class PlanGoalStepPolicy
{
    public function update(User $user, PlanGoalStep $step): bool
    {
        return $step->goal->monthPlan->user_id === $user->id;
    }

    public function delete(User $user, PlanGoalStep $step): bool
    {
        return $step->goal->monthPlan->user_id === $user->id;
    }
}
