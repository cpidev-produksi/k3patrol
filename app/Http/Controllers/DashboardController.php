<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $role = session('active_role');

        $base = Finding::query();

        if ($role === 'auditee') {
            $departmentIds = $user->departments()->pluck('departments.id')->toArray();
            $base->forDepartments($departmentIds);
        }

        $summary = (clone $base)->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $trend = (clone $base)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $overdue = (clone $base)->overdue()->with('department')->latest('due_at')->get();
        $nearDeadline = (clone $base)->nearDeadline()->with('department')->get();

        return view('dashboard', compact('summary', 'trend', 'overdue', 'nearDeadline'));
    }
}
