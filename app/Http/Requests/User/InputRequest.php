<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class InputRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    
    public function rules(): array
    {
        return [
            //
        ];
    }
}
