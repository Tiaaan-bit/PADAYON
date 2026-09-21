<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TherapistStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => ['required', 'email', 'max:255', 'unique:therapists,email'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'description' => ['nullable', 'string'],

            'specialty' => ['required', 'string', 'max:255'],

            'status' => ['required', 'in:available,unavailable'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
