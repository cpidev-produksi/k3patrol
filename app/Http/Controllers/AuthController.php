<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && !$user->is_active) {
            return back()->withErrors(['username' => 'Akun tidak aktif. Hubungi admin.'])->onlyInput('username');
        }

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        /** @var \App\Models\User $user */

        if ($user->hasMultipleRoles()) {
            return redirect()->route('select-role.form');
        }

        session(['active_role' => $user->defaultRole()]);

        return redirect()->route('dashboard');
    }

    public function selectRoleForm(): View|RedirectResponse
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */

        if (!$user->hasMultipleRoles()) {
            return redirect()->route('dashboard');
        }

        return view('auth.select-role');
    }

    public function setRole(Request $request): RedirectResponse
    {
        $request->validate(['role' => 'required|in:admin,auditor,auditee']);

        $user = Auth::user();
        /** @var \App\Models\User $user */
        abort_unless(in_array($request->role, $user->availableRoles(), true), 403, 'Anda tidak memiliki peran tersebut.');

        session(['active_role' => $request->role]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flush();

        return redirect('/login')
            ->with('message', 'Logout berhasil')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0, private',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }
}