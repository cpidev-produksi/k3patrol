@extends('layouts.app')
@section('title', 'Tambah Departemen')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Master Data</div>
            <h1 class="page-title">Tambah Departemen</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="max-width:520px;">
        <div class="card-body p-3 p-md-4">
            <form method="POST" action="{{ route('admin.departments.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Departemen</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="text" name="code" class="form-control mono" value="{{ old('code') }}" required>
                </div>
                <div class="k3-form-actions">
                    <button class="btn btn-primary px-4">Simpan</button>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
