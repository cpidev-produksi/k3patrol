@extends('layouts.app')
@section('title', 'Master Departemen')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Master Data</div>
            <h1 class="page-title">Departemen</h1>
            <p class="page-subtitle">Daftar departemen sebagai PIC (Auditee) atas temuan yang dilaporkan.</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">+ Tambah Departemen</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle k3-table">
            <thead><tr><th>Nama</th><th>Kode</th><th>Jumlah User</th><th width="160">Aksi</th></tr></thead>
            <tbody>
                @forelse ($departments as $d)
                    <tr>
                        <td data-label="Nama">{{ $d->name }}</td>
                        <td data-label="Kode" class="mono">{{ $d->code }}</td>
                        <td data-label="Jumlah User"><span class="badge bg-secondary">{{ $d->users_count }}</span></td>
                        <td data-label="Aksi">
                            <div class="k3-row-actions">
                                <a href="{{ route('admin.departments.edit', $d) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('admin.departments.destroy', $d) }}" method="POST"
                                      data-confirm="Departemen {{ $d->name }} akan dihapus." data-confirm-title="Hapus Departemen?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Belum ada departemen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $departments->links('pagination::bootstrap-5') }}
@endsection
