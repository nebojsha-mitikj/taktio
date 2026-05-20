<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PlanGoal;
use App\Models\User;

class PlanGoalPolicy
{
    public function update(User $user, PlanGoal $goal): bool
    {
        return $goal->monthPlan->user_id === $user->id;
    }

    public function delete(User $user, PlanGoal $goal): bool
    {
        return $goal->monthPlan->user_id === $user->id;
    }
}
