@extends('layouts.app')

@section('title', 'Buat Temuan')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Patroli Lapangan</div>
            <h1 class="page-title">Buat Temuan Baru</h1>
            <p class="page-subtitle">Catat potensi bahaya yang ditemukan saat patroli — lengkap dengan foto dan tingkat risiko.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger d-block">
            <div><i class="fa-solid fa-circle-exclamation me-1"></i><strong>Periksa kembali input Anda:</strong></div>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-3 p-sm-4">
            <form method="POST" action="{{ route('findings.store') }}" enctype="multipart/form-data"
                  data-confirm="Pastikan data dan foto sudah sesuai kondisi di lapangan. Temuan yang tersimpan akan langsung tercatat di sistem."
                  data-confirm-title="Simpan Temuan Baru?">
                @csrf

                <div class="row g-3 mb-1">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fa-solid fa-building"></i>Departemen (PIC)</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa-solid fa-tags"></i>Kategori</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">-- Others --</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3" id="category-other-wrap">
                    <label class="form-label"><i class="fa-solid fa-pen"></i>Kategori Lainnya (jika "Others")</label>
                    <input type="text" name="category_other" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-align-left"></i>Deskripsi Temuan</label>
                    <textarea name="description" class="form-control" rows="3" required placeholder="Jelaskan potensi bahaya yang ditemukan..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-location-dot"></i>Detail Area</label>
                    <input type="text" name="area_detail" class="form-control" placeholder="mis. Gudang B, dekat rak 12">
                </div>

                <div class="mb-4">
                    <label class="form-label d-block"><i class="fa-solid fa-gauge-simple-high"></i>Level Risiko</label>
                    <div class="row g-2">
                        @foreach ([
                            'Extreme' => ['24 jam', 'extreme', 'fa-solid fa-skull-crossbones'],
                            'High'    => ['7 hari',  'high',    'fa-solid fa-triangle-exclamation'],
                            'Medium'  => ['30 hari', 'medium',  'fa-solid fa-circle-exclamation'],
                            'Low'     => ['90 hari', 'low',     'fa-solid fa-circle-check'],
                        ] as $level => $meta)
                            <div class="col-6 col-md-3">
                                <label class="form-check d-flex align-items-center gap-2 border rounded-2 p-2 risk-pick risk-pick--{{ $meta[1] }}" style="border-color:var(--line) !important;">
                                    <input class="form-check-input mt-0" type="radio" name="risk_level" value="{{ $level }}" required>
                                    <span>
                                        <strong class="d-flex align-items-center gap-1" style="font-family:'Arvo'; font-size:15px;">
                                            <i class="{{ $meta[2] }}"></i> {{ $level }}
                                        </strong>
                                        <span class="text-muted" style="font-size:11.5px;">target {{ $meta[0] }}</span>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4 k3-photo-field">
                    <label class="form-label"><i class="fa-solid fa-camera"></i>Foto Temuan</label>
                    <input type="file" name="photo" id="photo-input" accept="image/*" class="k3-file-native" required>
                    <div class="k3-photo-actions">
                        <button type="button" class="btn btn-outline-secondary"
                                data-photo-source="camera">
                            <i class="fa-solid fa-camera"></i> Ambil Foto
                        </button>
                        <button type="button" class="btn btn-outline-secondary"
                                data-photo-source="gallery">
                            <i class="fa-solid fa-images"></i> Pilih dari Galeri
                        </button>
                    </div>
                    <div class="k3-file-hint"><i class="fa-solid fa-circle-info"></i><span id="photo-file-name">Belum ada foto dipilih. Maksimal ukuran 8 MB.</span></div>
                    <div id="photo-preview-wrap" class="k3-photo-preview-wrap">
                        <img id="photo-preview" class="k3-file-preview" alt="Pratinjau foto temuan">
                        <button type="button" id="photo-remove" class="k3-photo-remove" aria-label="Hapus foto yang dipilih" title="Hapus foto">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid d-sm-flex gap-2">
                    <button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk"></i> Simpan Temuan</button>
                    <a href="{{ route('findings.index') }}" class="btn btn-outline-secondary px-4"
                       data-confirm="Data yang sudah diisi pada form ini akan hilang."
                       data-confirm-title="Batalkan Input Temuan?">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('category_id').addEventListener('change', function () {
    document.getElementById('category-other-wrap').style.display = this.value ? 'none' : 'block';
});

document.querySelectorAll('.risk-pick input[type="radio"]').forEach(input => {
    input.addEventListener('change', () => {
        document.querySelectorAll('.risk-pick').forEach(el => el.classList.remove('is-checked'));
        input.closest('.risk-pick').classList.add('is-checked');
    });
});

const photoInput = document.getElementById('photo-input');
document.querySelectorAll('[data-photo-source]').forEach(button => {
    button.addEventListener('click', () => {
        if (button.dataset.photoSource === 'camera') {
            photoInput.setAttribute('capture', 'environment');
        } else {
            photoInput.removeAttribute('capture');
        }
        photoInput.click();
    });
});

photoInput.addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photo-preview');
    const previewWrap = document.getElementById('photo-preview-wrap');
    document.getElementById('photo-file-name').textContent = file ? file.name : 'Belum ada foto dipilih. Maksimal ukuran 8 MB.';
    if (file) {
        if (preview.src.startsWith('blob:')) URL.revokeObjectURL(preview.src);
        preview.src = URL.createObjectURL(file);
        previewWrap.style.display = 'inline-block';
    } else {
        preview.removeAttribute('src');
        previewWrap.style.display = 'none';
    }
});

document.getElementById('photo-remove').addEventListener('click', function () {
    photoInput.value = '';
    photoInput.removeAttribute('capture');
    photoInput.dispatchEvent(new Event('change', { bubbles: true }));
});
</script>
@endpush