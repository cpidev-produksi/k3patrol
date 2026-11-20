<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount('users')->latest()->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());
        return redirect()->route('admin.departments.index')->with('success', 'Departemen ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());
        return redirect()->route('admin.departments.index')->with('success', 'Departemen diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->findings()->exists()) {
            return back()->with('error', 'Departemen masih memiliki temuan terkait.');
        }

        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Departemen dihapus.');
    }
}
