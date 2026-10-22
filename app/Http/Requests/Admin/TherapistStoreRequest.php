<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TherapistStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $therapist = $this->route('therapist');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => ['required', 'email', 'max:255', Rule::unique('therapists', 'email')],
            
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'description' => ['nullable', 'string'],

            'specialty' => ['required', 'string', 'max:255'],

            'status' => ['required', 'in:available,unavailable'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
