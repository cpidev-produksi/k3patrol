<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isUpdate = (bool) $userId;

        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8'],
            'is_active' => ['boolean'],
            'is_admin' => ['boolean'],
            'is_auditor' => ['boolean'],
            'is_auditee' => ['boolean'],
            // wajib isi minimal 1 departemen kalau user berperan sebagai auditee
            'departments' => ['required_if:is_auditee,1', 'array'],
            'departments.*' => ['exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'departments.required_if' => 'Auditee wajib bertanggung jawab atas minimal 1 departemen.',
        ];
    }
}
