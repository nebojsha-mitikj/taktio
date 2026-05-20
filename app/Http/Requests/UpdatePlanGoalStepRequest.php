<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $title
 * @property bool|null $completed
 */
class UpdatePlanGoalStepRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:500'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}
