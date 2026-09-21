<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddOnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'price' => ['required', 'numeric', 'min:0'],

            'duration_minutes' => ['required', 'integer', 'min:1'],

            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
