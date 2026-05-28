<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property bool $completed
 */
class UpdateTaskCompletedRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Task $task */
        $task = $this->route('task');

        return $task->date->isToday();
    }

    public function rules(): array
    {
        return [
            'completed' => ['required', 'boolean'],
        ];
    }
}
