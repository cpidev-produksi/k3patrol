<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * true  = akun langsung aktif setelah registrasi.
     * false = akun dibuat nonaktif dan harus diaktifkan admin (Master User) sebelum bisa login.
     */
    protected const AUTO_ACTIVATE = true;

    public function create(): View
    {
        return view('auth.register', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
            'is_auditor' => ['required_without:is_auditee', 'nullable', 'boolean'],
            'is_auditee' => ['nullable', 'boolean'],
            'departments' => ['required_if:is_auditee,1', 'nullable', 'array'],
            'departments.*' => ['integer', 'exists:departments,id'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan strip.',
            'username.unique' => 'Username sudah dipakai, silakan pilih yang lain.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'is_auditor.required_without' => 'Pilih minimal satu peran: Auditor atau Auditee.',
            'departments.required_if' => 'Pilih minimal satu departemen untuk peran Auditee.',
        ]);

        $isAuditee = $request->boolean('is_auditee');

        DB::transaction(function () use ($request, $data, $isAuditee) {
            $user = new User();

            // forceFill: peran & status tidak bergantung pada $fillable, dan is_admin dipaksa false.
            $user->forceFill([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_admin' => false,
                'is_auditor' => $request->boolean('is_auditor'),
                'is_auditee' => $isAuditee,
                'is_active' => static::AUTO_ACTIVATE,
            ])->save();

            if ($isAuditee) {
                $user->departments()->sync($data['departments'] ?? []);
            }
        });

        $message = static::AUTO_ACTIVATE
            ? 'Registrasi berhasil. Silakan login dengan akun Anda.'
            : 'Registrasi berhasil. Akun Anda menunggu diaktifkan oleh admin sebelum bisa digunakan untuk login.';

        return redirect()->route('login')->with('message', $message);
    }
}