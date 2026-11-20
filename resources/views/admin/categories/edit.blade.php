@extends('layouts.app')
@section('title', 'Edit Kategori')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Master Data</div>
            <h1 class="page-title">Edit Kategori</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="max-width:520px;">
        <div class="card-body p-3 p-md-4">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required autofocus>
                </div>
                <div class="k3-form-actions">
                    <button class="btn btn-primary px-4">Update</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
