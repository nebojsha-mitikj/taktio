<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskPriorityEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Task> */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => null,
            'date' => now()->toDateString(),
            'completed' => false,
            'priority' => TaskPriorityEnum::NONE,
            'recurring_task_template_id' => null,
        ];
    }

    public function dueToday(): static
    {
        return $this->state(['date' => now()->toDateString()]);
    }

    public function dueTomorrow(): static
    {
        return $this->state(['date' => now()->addDay()->toDateString()]);
    }

    public function completed(): static
    {
        return $this->state(['completed' => true]);
    }
}
