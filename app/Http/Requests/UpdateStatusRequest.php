<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @property TaskStatusEnum $status
 */
class UpdateStatusRequest extends FormRequest
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
            'status' => ['required', new Enum(TaskStatusEnum::class)],
        ];
    }
}
