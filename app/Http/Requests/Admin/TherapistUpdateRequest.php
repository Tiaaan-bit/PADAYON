<?php

namespace App\Http\Requests\Admin;

use App\Models\Therapists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TherapistUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Therapists $therapist */
        $therapist = $this->route('therapist');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => ['required', 'email', 'max:255', Rule::unique('therapists', 'email')->ignore($therapist->id)],

            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            'description' => ['nullable', 'string'],

            'specialty' => ['required', 'string', 'max:255'],

            'status' => ['required', 'in:available,unavailable'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
