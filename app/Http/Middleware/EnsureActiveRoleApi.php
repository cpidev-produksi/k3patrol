<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRoleApi
{
    /**
     * Versi API (stateless) dari EnsureActiveRole. Mobile app mengirim role
     * aktif via header "X-Active-Role" di tiap request (disimpan di
     * EncryptedSharedPreferences setelah user memilih role saat login).
     *
     * Pakai: ->middleware('active.role.api:admin') atau ->middleware('active.role.api:auditor')
     */
    public function handle(Request $request, Closure $next, string $requiredRole): Response
    {
        $user = $request->user();
        abort_unless($user, 401);

        $activeRole = $request->header('X-Active-Role');

        if (!$activeRole) {
            $activeRole = $user->defaultRole();
        }

        abort_unless($activeRole === $requiredRole, 403, 'Peran aktif tidak sesuai.');

        abort_unless(in_array($requiredRole, $user->availableRoles(), true), 403, 'Peran tidak valid untuk akun ini.');

        return $next($request);
    }
}
