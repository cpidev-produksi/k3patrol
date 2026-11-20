<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#16211C">
    <title>K3 Patrol - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&family=Roboto:wght@400;500;700&family=Rubik:wght@400;500;600;700;800&family=Ubuntu+Mono:wght@400;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="{{ asset('css/k3-theme.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="hazard-rail"></div>

    <nav class="navbar navbar-expand-lg k3-nav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/icon_k3.png') }}" alt="K3 Patrol" class="me-2">
                <span class="brand-text">K3 PATROL</span>
            </a>

            <span class="badge role-badge ms-auto me-2 d-lg-none">{{ session('active_role') }}</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#k3NavMenu"
                    aria-controls="k3NavMenu" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="k3NavMenu">
                <div class="navbar-nav">
                    <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                    <a class="nav-link @if(request()->routeIs('findings.*') && !request()->routeIs('findings.create')) active @endif" href="{{ route('findings.index') }}" @if(request()->routeIs('findings.*') && !request()->routeIs('findings.create')) aria-current="page" @endif>
                        <i class="fa-solid fa-clipboard-list"></i> Temuan
                    </a>
                    @if (session('active_role') === 'admin')
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle @if(request()->routeIs('admin.*')) active-parent @endif" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-database"></i> Master Data
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}"><i class="fa-solid fa-users me-2"></i>Master User</a></li>
                                <li><a class="dropdown-item @if(request()->routeIs('admin.departments.*')) active @endif" href="{{ route('admin.departments.index') }}"><i class="fa-solid fa-building me-2"></i>Master Departemen</a></li>
                                <li><a class="dropdown-item @if(request()->routeIs('admin.categories.*')) active @endif" href="{{ route('admin.categories.index') }}"><i class="fa-solid fa-tags me-2"></i>Master Kategori Temuan</a></li>
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="k3-nav-actions ms-lg-auto">
                    <span class="badge role-badge d-none d-lg-inline-block">{{ session('active_role') }}</span>
                    @if (auth()->user()->hasMultipleRoles())
                        <a href="{{ route('select-role.form') }}" class="btn btn-sm btn-outline-light">
                            <i class="fa-solid fa-repeat"></i> Ganti Peran
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}"
                          data-confirm="Anda akan keluar dari sesi K3 Patrol saat ini."
                          data-confirm-title="Keluar dari Akun?">
                        @csrf
                        <button class="btn btn-sm btn-outline-light"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container page-shell">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    @if (session('active_role') === 'auditor' && !request()->routeIs('findings.create'))
        <a href="{{ route('findings.create') }}" class="k3-fab" aria-label="Buat Temuan Baru" title="Buat Temuan Baru">
            <i class="fa-solid fa-plus"></i><span class="k3-fab-label">Buat Temuan</span>
        </a>
    @endif

    {{-- Global OK / Cancel confirmation modal — triggered by data-confirm on any <form> or <a> --}}
    <div class="modal fade" id="k3ConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content k3-confirm">
                <div class="modal-body text-center p-4">
                    <div class="k3-confirm-icon mb-3"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h5 class="k3-confirm-title mb-2" id="k3ConfirmTitle">Konfirmasi Tindakan</h5>
                    <p class="text-muted mb-4" id="k3ConfirmText">Apakah Anda yakin?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary px-4" id="k3ConfirmOk">
                            <i class="fa-solid fa-check"></i> OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Reusable OK/Cancel confirmation for any form or link with a `data-confirm` attribute.
        (function () {
            var modalEl = document.getElementById('k3ConfirmModal');
            var confirmModal = new bootstrap.Modal(modalEl);
            var titleEl = document.getElementById('k3ConfirmTitle');
            var textEl = document.getElementById('k3ConfirmText');
            var okBtn = document.getElementById('k3ConfirmOk');
            var pendingAction = null;

            document.addEventListener('submit', function (e) {
                var form = e.target.closest('form[data-confirm]');
                if (form && !form.dataset.k3Confirmed) {
                    e.preventDefault();
                    titleEl.textContent = form.dataset.confirmTitle || 'Konfirmasi Tindakan';
                    textEl.textContent = form.dataset.confirm;
                    pendingAction = function () {
                        form.dataset.k3Confirmed = '1';
                        form.requestSubmit ? form.requestSubmit() : form.submit();
                    };
                    confirmModal.show();
                }
            }, true);

            document.addEventListener('click', function (e) {
                var link = e.target.closest('a[data-confirm]');
                if (link) {
                    e.preventDefault();
                    titleEl.textContent = link.dataset.confirmTitle || 'Konfirmasi';
                    textEl.textContent = link.dataset.confirm;
                    pendingAction = function () { window.location.href = link.href; };
                    confirmModal.show();
                }
            });

            okBtn.addEventListener('click', function () {
                confirmModal.hide();
                var action = pendingAction;
                pendingAction = null;
                if (action) action();
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>