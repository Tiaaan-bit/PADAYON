<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facebook_name' => ['required', 'string', 'max:255'],

            'facebook_url' => ['required', 'url', 'max:2048'],

            'business_address' => ['required', 'string', 'max:1000'],

            'location_url' => ['nullable', 'url', 'max:2048'],

            'phone_number' => ['required', 'string', 'max:30'],

            'opening_time' => ['required', 'date_format:H:i'],

            'closing_time' => ['required', 'date_format:H:i'],

            'website_name' => ['required', 'string', 'max:255'],
            
            'website_subname' => ['required', 'string', 'max:255'],

            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
