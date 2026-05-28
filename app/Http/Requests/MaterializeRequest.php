<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $date
 * @property bool|null $completed
 */
class MaterializeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'completed' => ['nullable', 'boolean'],
        ];
    }
}
