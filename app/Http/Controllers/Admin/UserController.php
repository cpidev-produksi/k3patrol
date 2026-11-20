<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('departments')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->departments()->sync($data['departments'] ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User ditambahkan.');
    }

    public function edit(User $user): View
    {
        $departments = Department::orderBy('name')->get();
        $user->load('departments');
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->departments()->sync($data['departments'] ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->findingsAsAuditor()->exists() || $user->responses()->exists()) {
            return back()->with('error', 'User memiliki riwayat temuan/respon dan tidak bisa dihapus.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User dihapus.');
    }
}
