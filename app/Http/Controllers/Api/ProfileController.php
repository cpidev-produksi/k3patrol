<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('departments');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'is_auditor' => $user->is_auditor,
                'is_auditee' => $user->is_auditee,
                'departments' => $user->departments->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'code' => $d->code,
                ]),
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'password' => ['nullable', 'confirmed', 'string', Rule::when(
                $request->filled('password'), ['min:8'], []
            )],
        ]);

        $user->name = $validated['name'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return $this->show($request);
    }
}
