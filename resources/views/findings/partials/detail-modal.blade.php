<div class="k3-detail">

    {{-- Banner: foto fit to content + info penting --}}
    @php $heroPhoto = $finding->photos->where('type', 'temuan')->first(); @endphp
    <div class="k3-banner">
        @if ($heroPhoto)
            <div class="k3-banner-bg" style="background-image:url('{{ $heroPhoto->url }}')"></div>
            <a href="{{ $heroPhoto->url }}" target="_blank" rel="noopener">
                <img src="{{ $heroPhoto->url }}" alt="Foto temuan {{ $finding->finding_number }}" class="k3-banner-img">
            </a>
        @else
            <div class="k3-banner-empty"><i class="fa-solid fa-image"></i></div>
        @endif
        <div class="k3-banner-overlay">
            <div class="k3-banner-no"><small>No. Temuan</small>{{ $finding->finding_number }}</div>
            <div class="k3-banner-pills">
                @include('findings.partials.risk-badge', ['level' => $finding->risk_level])
                @include('findings.partials.status-badge', ['finding' => $finding])
            </div>
        </div>
    </div>

    <div class="k3-detail-body">

    {{-- Highlight bar: risiko, departemen, area, due --}}
    <div class="k3-highlight-bar mb-3">
        <div class="k3-highlight-meta">
            <span><i class="fa-solid fa-building"></i>{{ $finding->department->name }}</span>
            <span><i class="fa-solid fa-location-dot"></i>{{ $finding->area_detail ?? 'Area tidak dicatat' }}</span>
            <span><i class="fa-regular fa-clock"></i>Due {{ $finding->due_at->format('d M Y H:i') }}</span>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <span class="text-muted small d-block"><i class="fa-solid fa-tags me-1"></i>Kategori</span>
            {{ $finding->category->name ?? $finding->category_other }}
        </div>
        <div class="col-6">
            <span class="text-muted small d-block"><i class="fa-solid fa-user-pen me-1"></i>Dibuat oleh</span>
            {{ $finding->auditor->name }}
        </div>
    </div>

    {{-- Deskripsi — dihighlight sebagai callout --}}
    <div class="k3-desc-box mb-4">
        <span class="text-muted small d-block mb-1"><i class="fa-solid fa-align-left me-1"></i>Deskripsi Temuan</span>
        {{ $finding->description }}
    </div>

    {{-- Foto temuan — dihighlight sebagai galeri --}}
    <h6 class="k3-section-title"><i class="fa-solid fa-camera"></i>Foto Temuan</h6>
    <div class="k3-photo-grid mb-4">
        @forelse ($finding->photos->where('type', 'temuan') as $p)
            <a href="{{ $p->url }}" target="_blank" rel="noopener" class="k3-photo-thumb">
                <img src="{{ $p->url }}" alt="Foto temuan {{ $finding->finding_number }}">
                <span class="k3-photo-zoom"><i class="fa-solid fa-expand"></i></span>
            </a>
        @empty
            <span class="text-muted small"><i class="fa-solid fa-image me-1"></i>Tidak ada foto.</span>
        @endforelse
    </div>

    @if ($finding->response)
        <hr class="section-divider">
        <h6 class="k3-section-title"><i class="fa-solid fa-person-digging"></i>Tindak Lanjut Auditee</h6>
        <div class="k3-timeline-item mb-2">
            <p class="mb-1">{{ $finding->response->description_perbaikan }}</p>
            <p class="text-muted small mb-0">
                <i class="fa-regular fa-clock me-1"></i>Direspon: {{ $finding->response->responded_at->format('d M Y H:i') }} oleh {{ $finding->response->auditee->name }}
            </p>
        </div>

        <span class="text-muted small d-block mb-2"><i class="fa-solid fa-camera me-1"></i>Foto Perbaikan</span>
        <div class="k3-photo-grid mb-3">
            @forelse ($finding->photos->where('type', 'perbaikan') as $p)
                <a href="{{ $p->url }}" target="_blank" rel="noopener" class="k3-photo-thumb">
                    <img src="{{ $p->url }}" alt="Foto perbaikan {{ $finding->finding_number }}">
                    <span class="k3-photo-zoom"><i class="fa-solid fa-expand"></i></span>
                </a>
            @empty
                <span class="text-muted small"><i class="fa-solid fa-image me-1"></i>Tidak ada foto.</span>
            @endforelse
        </div>
    @endif

    @if ($finding->verification)
        <hr class="section-divider">
        <h6 class="k3-section-title"><i class="fa-solid fa-shield-halved"></i>Verifikasi Auditor</h6>
        <div class="k3-timeline-item k3-timeline-item--verified">
            <p class="text-muted small mb-1">
                <i class="fa-regular fa-clock me-1"></i>Diverifikasi: {{ $finding->verification->verified_at->format('d M Y H:i') }} oleh {{ $finding->verification->auditor->name }}
            </p>
            @if ($finding->verification->note)
                <p class="mb-0">Catatan: {{ $finding->verification->note }}</p>
            @endif
        </div>
    @endif

    @if (session('active_role') === 'auditee' && in_array($finding->status, ['Open', 'On Process']))
        <hr class="section-divider">
        <h6 class="k3-section-title"><i class="fa-solid fa-wrench"></i>Tindak Lanjuti Temuan Ini</h6>
        <form method="POST" action="{{ route('findings.respond', $finding) }}" enctype="multipart/form-data"
              data-confirm="Tindak lanjut akan dikirim ke auditor untuk diverifikasi dan tidak dapat diedit lagi."
              data-confirm-title="Simpan Tindak Lanjut?">
            @csrf
            <div class="mb-2">
                <label class="form-label"><i class="fa-solid fa-align-left"></i>Deskripsi Perbaikan</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi perbaikan" required></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label"><i class="fa-solid fa-circle-dot"></i>Status</label>
                <select name="status" class="form-select" required>
                    <option value="On Process">On Process</option>
                    <option value="Closed">Closed</option>
                </select>
            </div>
            {{-- Konten modal dimuat lewat fetch (innerHTML), jadi <script> tidak jalan: pakai handler inline. --}}
            <div class="mb-2 k3-photo-field">
                <label class="form-label"><i class="fa-solid fa-camera"></i>Foto Perbaikan</label>
                <input type="file" name="photo" accept="image/*" class="k3-file-native" required
                       onchange="this.closest('.k3-photo-field').querySelector('[data-photo-name]').textContent = this.files[0] ? this.files[0].name : 'Belum ada foto dipilih.'">
                <div class="k3-photo-actions">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="var i=this.closest('.k3-photo-field').querySelector('input[type=file]');i.setAttribute('capture','environment');i.click();">
                        <i class="fa-solid fa-camera"></i> Ambil Foto
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="var i=this.closest('.k3-photo-field').querySelector('input[type=file]');i.removeAttribute('capture');i.click();">
                        <i class="fa-solid fa-images"></i> Pilih dari Galeri
                    </button>
                </div>
                <div class="k3-file-hint"><i class="fa-solid fa-circle-info"></i><span data-photo-name>Belum ada foto dipilih.</span></div>
            </div>
            <button class="btn btn-success btn-sm"><i class="fa-solid fa-floppy-disk"></i> Simpan Tindak Lanjut</button>
        </form>
    @endif

    @if (session('active_role') === 'auditor' && $finding->status === 'Closed')
        <hr class="section-divider">
        <form method="POST" action="{{ route('findings.verify', $finding) }}"
              data-confirm="Status temuan akan menjadi Closed Verified dan tidak dapat diubah kembali."
              data-confirm-title="Verifikasi Perbaikan?">
            @csrf
            <div class="mb-2">
                <label class="form-label"><i class="fa-solid fa-note-sticky"></i>Catatan Verifikasi (opsional)</label>
                <input type="text" name="note" class="form-control" placeholder="Catatan verifikasi (opsional)">
            </div>
            <button class="btn btn-primary btn-sm"><i class="fa-solid fa-shield-halved"></i> Verifikasi Perbaikan</button>
        </form>
    @endif

    {{-- Hapus temuan: hanya tampil untuk admin (server tetap memvalidasi lewat route + controller) --}}
    @if (session('active_role') === 'admin')
        <hr class="section-divider">
        <form method="POST" action="{{ route('findings.destroy', $finding) }}" class="k3-danger-zone"
              data-confirm="Temuan {{ $finding->finding_number }} beserta foto, tindak lanjut, dan verifikasinya akan dihapus. Tindakan ini tidak dapat dibatalkan."
              data-confirm-title="Hapus Temuan?">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Hapus Temuan</button>
        </form>
    @endif
    </div>
</div>