<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskRecurEnum;
use App\Models\RecurringTaskTemplate;
use App\Models\RecurringTaskTemplatePeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RecurringTaskTemplate> */
class RecurringTaskTemplateFactory extends Factory
{
    protected $model = RecurringTaskTemplate::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => null,
            'recur' => TaskRecurEnum::DAILY,
            'priority' => TaskPriorityEnum::NONE,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (RecurringTaskTemplate $template) {
            RecurringTaskTemplatePeriod::create([
                'recurring_task_template_id' => $template->id,
                'start_date' => now()->toDateString(),
                'end_date' => null,
            ]);
        });
    }

    public function weekly(): static
    {
        return $this->state(['recur' => TaskRecurEnum::WEEKLY]);
    }
}
