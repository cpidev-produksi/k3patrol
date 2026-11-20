<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuditeeController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// ---------- Guest ----------
Route::middleware(['guest', 'no-cache'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Registrasi mandiri (Auditor / Auditee). Akun Admin hanya dibuat oleh admin lewat Master User.
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:6,1')->name('register.submit');
});

// ---------- Authenticated ----------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/select-role', [AuthController::class, 'selectRoleForm'])->name('select-role.form');
    Route::post('/select-role', [AuthController::class, 'setRole'])->name('select-role.submit');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Daftar & detail temuan bisa diakses kedua role (isi disaring per-role di controller)
    Route::get('/findings', [FindingController::class, 'index'])->name('findings.index');
    Route::get('/findings/{finding}/detail', [FindingController::class, 'detailModal'])->name('findings.detail');

    // ---------- Khusus role Auditor ----------
    Route::middleware('active.role:auditor')->group(function () {
        Route::get('/findings/create', [FindingController::class, 'create'])->name('findings.create');
        Route::post('/findings', [FindingController::class, 'store'])->name('findings.store');
        Route::post('/findings/{finding}/verify', [AuditorController::class, 'verify'])->name('findings.verify');
    });

    // Master data + hapus temuan: hanya untuk admin
    Route::middleware('active.role:admin')->group(function () {
        Route::delete('/findings/{finding}', [FindingController::class, 'destroy'])->name('findings.destroy');

        Route::resource('admin/categories', CategoryController::class)
            ->parameters(['categories' => 'category'])
            ->names('admin.categories');
        Route::resource('admin/departments', DepartmentController::class)
            ->parameters(['departments' => 'department'])
            ->names('admin.departments');
        Route::resource('admin/users', UserController::class)
            ->parameters(['users' => 'user'])
            ->names('admin.users');
    });

    // ---------- Khusus role Auditee ----------
    Route::middleware('active.role:auditee')->group(function () {
        Route::post('/findings/{finding}/respond', [AuditeeController::class, 'respond'])->name('findings.respond');
    });
});

Route::get('/', fn () => redirect()->route('dashboard'));