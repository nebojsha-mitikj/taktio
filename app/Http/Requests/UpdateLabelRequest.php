<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 */
class UpdateLabelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('labels', 'name')
                    ->where('user_id', $this->user()->id)
                    ->ignore($this->route('label')->id)],
        ];
    }
}
