<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskPriorityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * @property string $title
 * @property string|null $description
 * @property string $date
 * @property string $priority
 * @property array<int> $label_ids
 * @property int|null $recurring_task_template_id
 */
class StoreTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => ['required', 'date', 'after_or_equal:'.now()->startOfDay()->toDateString()],
            'priority' => ['required', new Enum(TaskPriorityEnum::class)],
            'label_ids' => ['present', 'array'],
            'label_ids.*' => [
                'integer',
                Rule::exists('labels', 'id')
                    ->where('user_id', $this->user()->id),
            ],
            'recurring_task_template_id' => [
                'nullable',
                'integer',
                Rule::exists('recurring_task_templates', 'id')
                    ->where('user_id', $this->user()->id),
            ],
        ];
    }
}
