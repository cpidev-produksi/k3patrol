@extends('layouts.app')
@section('title', 'Master Kategori')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Master Data</div>
            <h1 class="page-title">Kategori Temuan</h1>
            <p class="page-subtitle">Daftar kategori yang digunakan auditor saat mencatat temuan.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle k3-table">
            <thead><tr><th>Nama</th><th width="160">Aksi</th></tr></thead>
            <tbody>
                @forelse ($categories as $c)
                    <tr>
                        <td data-label="Nama">{{ $c->name }}</td>
                        <td data-label="Aksi">
                            <div class="k3-row-actions">
                                <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $c) }}" method="POST"
                                      data-confirm="Kategori {{ $c->name }} akan dihapus." data-confirm-title="Hapus Kategori?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="empty-state">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $categories->links('pagination::bootstrap-5') }}
@endsection
