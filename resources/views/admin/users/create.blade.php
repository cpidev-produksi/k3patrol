@extends('layouts.app')
@section('title', 'Tambah User')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Master Data</div>
            <h1 class="page-title">Tambah User</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card" style="max-width:640px;">
        <div class="card-body p-3 p-md-4">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <hr class="section-divider">

                <label class="form-label d-block">Peran</label>
                <div class="d-flex gap-3 flex-wrap mb-3">
                    <label class="form-check d-flex align-items-center gap-2 border rounded-2 px-3 py-2" style="border-color:var(--line) !important; cursor:pointer;">
                        <input class="form-check-input mt-0" type="checkbox" name="is_admin" value="1" id="is_admin">
                        <span>Admin</span>
                    </label>
                    <label class="form-check d-flex align-items-center gap-2 border rounded-2 px-3 py-2" style="border-color:var(--line) !important; cursor:pointer;">
                        <input class="form-check-input mt-0" type="checkbox" name="is_auditor" value="1" id="is_auditor">
                        <span>Auditor</span>
                    </label>
                    <label class="form-check d-flex align-items-center gap-2 border rounded-2 px-3 py-2" style="border-color:var(--line) !important; cursor:pointer;">
                        <input class="form-check-input mt-0" type="checkbox" name="is_auditee" value="1" id="is_auditee">
                        <span>Auditee</span>
                    </label>
                </div>

                @php($selectedDepartments = old('departments', []))
                <div class="mb-3">
                    <label class="form-label">Departemen yang diampu (untuk Auditee, bisa pilih lebih dari 1)</label>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach ($departments as $d)
                            <label class="form-check d-flex align-items-center gap-2 border rounded-2 px-3 py-2" style="border-color:var(--line) !important; cursor:pointer;">
                                <input class="form-check-input mt-0" type="checkbox" name="departments[]" value="{{ $d->id }}" @checked(in_array($d->id, $selectedDepartments))>
                                <span>{{ $d->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', 1) == 1)>
                    <label class="form-check-label" for="is_active" style="text-transform:none; font-weight:500; letter-spacing:0; color:var(--ink);" data-active-label>Tidak Aktif</label>
                </div>

                <div class="k3-form-actions">
                    <button class="btn btn-primary px-4">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const activeToggle = document.getElementById('is_active');
const activeLabel = document.querySelector('[data-active-label]');

function updateActiveLabel() {
    activeLabel.textContent = activeToggle.checked ? 'Aktif' : 'Tidak Aktif';
}

activeToggle.addEventListener('change', updateActiveLabel);
updateActiveLabel();
</script>
@endpush
