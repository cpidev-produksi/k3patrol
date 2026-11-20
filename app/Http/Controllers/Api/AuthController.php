<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
        ]);

        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Akun tidak aktif.'],
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'is_admin' => $user->is_admin,
                'is_auditor' => $user->is_auditor,
                'is_auditee' => $user->is_auditee,
                'needs_role_selection' => $user->hasMultipleRoles(),
            ],
        ]);
    }

    /**
     * Pilih role aktif. Karena API stateless, role disimpan di client
     * (dikirim ulang lewat header/parameter tiap request) atau lewat token abilities.
     * Di sini disederhanakan: server hanya memvalidasi lalu client menyimpannya.
     */
    public function setRole(Request $request): JsonResponse
    {
        $request->validate(['role' => 'required|in:admin,auditor,auditee']);

        $user = $request->user();
        abort_unless(in_array($request->role, $user->availableRoles(), true), 403, 'Anda tidak memiliki peran tersebut.');

        return response()->json(['active_role' => $request->role]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => ['required', 'string']]);
        $request->user()->update(['fcm_token' => $request->fcm_token]);
        return response()->json(['message' => 'FCM token updated']);
    }
}
