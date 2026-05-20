<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $main_goal
 */
class UpdatePlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'main_goal' => ['nullable', 'string', 'max:500'],
        ];
    }
}
