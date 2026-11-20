<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRole
{
    /**
     * Batasi akses route sesuai session('active_role').
     * Pakai: ->middleware('active.role:admin') , ->middleware('active.role:auditor') atau ->middleware('active.role:auditee')
     */
    public function handle(Request $request, Closure $next, string $requiredRole): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $activeRole = session('active_role');

        // Jika user hanya punya satu role, anggap itu active_role secara implisit.
        if (!$activeRole) {
            $activeRole = $user->defaultRole();
        }

        if ($activeRole !== $requiredRole) {
            abort(403, 'Anda tidak memiliki akses untuk peran ini.');
        }

        // Pastikan user memang punya flag role tsb di database (bukan hanya session lama)
        if (!in_array($requiredRole, $user->availableRoles(), true)) {
            abort(403, 'Peran tidak valid untuk akun ini.');
        }

        return $next($request);
    }
}
