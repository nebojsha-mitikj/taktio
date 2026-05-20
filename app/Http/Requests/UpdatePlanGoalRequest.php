<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $title
 * @property bool|null $completed
 */
class UpdatePlanGoalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:500'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}
