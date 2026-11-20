<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondFindingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in(['On Process', 'Closed'])],
            'photo' => ['required', 'image', 'max:8192'],
        ];
    }
}
