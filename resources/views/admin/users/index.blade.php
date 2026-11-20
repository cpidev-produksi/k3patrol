@extends('layouts.app')
@section('title', 'Master User')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Master Data</div>
            <h1 class="page-title">Pengguna</h1>
            <p class="page-subtitle">Kelola akun, peran (Admin / Auditor / Auditee), dan departemen yang diampu.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah User</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle k3-table">
            <thead>
                <tr>
                    <th>Nama</th><th>Username</th><th>Role</th><th>Departemen</th><th>Aktif</th><th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr>
                        <td data-label="Nama">{{ $u->name }}</td>
                        <td data-label="Username" class="mono">{{ $u->username }}</td>
                        <td data-label="Role">
                            <span class="k3-td-badges">
                            @if ($u->is_admin) <span class="badge bg-dark">Admin</span> @endif
                            @if ($u->is_auditor) <span class="badge bg-primary">Auditor</span> @endif
                            @if ($u->is_auditee) <span class="badge bg-success">Auditee</span> @endif
                            </span>
                        </td>
                        <td data-label="Departemen">{{ $u->departments->pluck('name')->join(', ') ?: '-' }}</td>
                        <td data-label="Aktif">
                            @if ($u->is_active)
                                <span class="badge bg-primary">Ya</span>
                            @else
                                <span class="badge bg-secondary">Tidak</span>
                            @endif
                        </td>
                        <td data-label="Aksi">
                            <div class="k3-row-actions">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST"
                                      data-confirm="User {{ $u->name }} akan dihapus dan tidak bisa login lagi." data-confirm-title="Hapus User?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links('pagination::bootstrap-5') }}
@endsection
