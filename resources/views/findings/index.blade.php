@extends('layouts.app')

@section('title', 'Daftar Temuan')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Temuan K3</div>
            <h1 class="page-title">Daftar Temuan</h1>
            <p class="page-subtitle">Semua temuan hasil patroli, mulai dari lapor hingga terverifikasi.</p>
        </div>
        @if (session('active_role') === 'auditor')
            <a href="{{ route('findings.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-circle-plus"></i> Buat Temuan
            </a>
        @endif
    </div>

    <div class="card mb-3 k3-filter-card">
        <div class="card-body">
            @php
                $statusTabs = [
                    'Open' => 'Open',
                    'On Process' => 'On Process',
                    'Closed' => 'Close Unverified',
                    'Closed Verified' => 'Verified',
                ];
            @endphp
            <div class="nav nav-tabs k3-status-tabs mb-3" role="tablist" aria-label="Filter status temuan">
                @foreach ($statusTabs as $statusValue => $statusLabel)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $statusValue, 'page' => null]) }}"
                       class="nav-link @if(request('status') === $statusValue) active @endif"
                       @if(request('status') === $statusValue) aria-current="page" @endif>
                        {{ $statusLabel }}
                    </a>
                @endforeach
            </div>
            <button class="btn btn-sm btn-outline-secondary d-md-none w-100 mb-3 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <span><i class="fa-solid fa-sliders me-2"></i>Filter Temuan</span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="collapse d-md-block" id="filterCollapse">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-6 col-md-auto">
                        <label class="form-label d-block mb-1"><i class="fa-solid fa-triangle-exclamation"></i>Risiko</label>
                        <select name="risk_level" class="form-select form-select-sm">
                            <option value="">Semua Risiko</option>
                            @foreach (['Extreme', 'High', 'Medium', 'Low'] as $r)
                                <option value="{{ $r }}" @selected(request('risk_level') === $r)>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-auto">
                        <label class="form-label d-block mb-1"><i class="fa-solid fa-building"></i>Departemen</label>
                        <select name="department_id" class="form-select form-select-sm">
                            <option value="">Semua Departemen</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}" @selected((string) request('department_id') === (string) $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-auto">
                        <label class="form-label d-block mb-1"><i class="fa-solid fa-tags"></i>Kategori</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" @selected((string) request('category_id') === (string) $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-auto">
                        <label class="form-label d-block mb-1"><i class="fa-regular fa-calendar"></i>Dari</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-auto">
                        <label class="form-label d-block mb-1"><i class="fa-regular fa-calendar"></i>Sampai</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                        <a href="{{ route('findings.index') }}" class="btn btn-sm btn-link"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle k3-table">
            <thead>
                <tr>
                    <th>No. Temuan</th>
                    <th>Departemen</th>
                    <th>Kategori</th>
                    <th>Risiko</th>
                    <th>Status</th>
                    <th>Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($findings as $f)
                    <tr role="button" class="finding-row" data-id="{{ $f->id }}" tabindex="0">
                        <td data-label="No. Temuan"><i class="fa-solid fa-hashtag k3-td-icon"></i><span class="mono">{{ $f->finding_number }}</span></td>
                        <td data-label="Departemen">{{ $f->department->name }}</td>
                        <td data-label="Kategori">{{ $f->category->name ?? '-' }}</td>
                        <td data-label="Risiko">@include('findings.partials.risk-badge', ['level' => $f->risk_level])</td>
                        <td data-label="Status">@include('findings.partials.status-badge', ['finding' => $f])</td>
                        <td data-label="Due"><i class="fa-regular fa-clock k3-td-icon"></i>{{ $f->due_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fa-solid fa-clipboard-check"></i>
                                Belum ada temuan yang cocok dengan filter ini.
                                @if (session('active_role') === 'auditor')
                                    <div class="mt-3">
                                        <a href="{{ route('findings.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-circle-plus"></i> Buat Temuan Pertama
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $findings->links('pagination::bootstrap-5') }}

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <button type="button" class="btn-close-float" data-bs-dismiss="modal" aria-label="Tutup"><i class="visually-hidden">Tutup</i></button>
                <div class="modal-body" id="modal-body">
                    <div class="empty-state">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        Memuat...
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

function openFindingDetail(id) {
    document.getElementById('modal-body').innerHTML = '<div class="empty-state"><i class="fa-solid fa-spinner fa-spin"></i>Memuat...</div>';
    detailModal.show();

    fetch(`/findings/${id}/detail`)
        .then(res => res.text())
        .then(html => { document.getElementById('modal-body').innerHTML = html; })
        .catch(() => {
            document.getElementById('modal-body').innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i>Gagal memuat detail temuan.</div>';
        });
}

document.querySelectorAll('.finding-row').forEach(row => {
    row.addEventListener('click', () => openFindingDetail(row.dataset.id));
    row.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') openFindingDetail(row.dataset.id);
    });
});
</script>
@endpush