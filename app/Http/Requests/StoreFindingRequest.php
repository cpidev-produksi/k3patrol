<?php

namespace App\Http\Requests;

use App\Models\Finding;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFindingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'category_other' => ['required_without:category_id', 'nullable', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'area_detail' => ['nullable', 'string', 'max:255'],
            'risk_level' => ['required', Rule::in(array_keys(Finding::RISK_SLA_HOURS))],
            'photo' => ['required', 'image', 'max:8192'],
        ];
    }
}
