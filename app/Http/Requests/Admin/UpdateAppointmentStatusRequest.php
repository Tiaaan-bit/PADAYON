<?php

namespace App\Http\Requests\Admin;

use App\Enums\Admin\Appointment\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(array_map(fn(AppointmentStatus $status) => $status->value, AppointmentStatus::cases()))],
        ];
    }
}
