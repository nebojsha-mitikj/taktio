<?php

namespace App\Http\Requests;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskRecurEnum;
use App\Enums\WeekdayEnum;
use App\Traits\RecurringTaskTemplateRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $title
 * @property string|null $description
 * @property TaskPriorityEnum $priority
 * @property TaskRecurEnum $recur
 * @property array<int, WeekdayEnum> $weekdays
 * @property array $label_ids
 */
class StoreRecurringTaskTemplateRequest extends FormRequest
{
    use RecurringTaskTemplateRules;

    public function rules(): array
    {
        return $this->recurringRules();
    }
}
