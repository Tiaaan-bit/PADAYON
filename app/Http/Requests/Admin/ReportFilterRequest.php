<?php

namespace App\Http\Requests\Admin;

use App\Enums\Admin\Report\ReportPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => ['nullable', Rule::enum(ReportPeriod::class)],
        ];
    }

    public function period(): ReportPeriod
    {
        return ReportPeriod::tryFrom($this->input('period', ReportPeriod::TODAY->value)) ?? ReportPeriod::TODAY;
    }
}
