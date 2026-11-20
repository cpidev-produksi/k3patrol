@extends('layouts.app')

@section('title', 'Dashboard')

@php
    // ---------- Data status (urutan = alur kerja temuan) ----------
    $statusMeta = [
        'Open'            => ['color' => '#D81E14', 'icon' => 'fa-folder-open',    'hint' => 'Belum ditindaklanjuti'],
        'On Process'      => ['color' => '#F2A20C', 'icon' => 'fa-person-digging', 'hint' => 'Sedang diperbaiki'],
        'Closed'          => ['color' => '#5B665F', 'icon' => 'fa-circle-check',   'hint' => 'Menunggu verifikasi auditor'],
        'Closed Verified' => ['color' => '#0E6B3B', 'icon' => 'fa-shield-halved',  'hint' => 'Selesai dan terverifikasi'],
    ];
    $counts = [];
    foreach ($statusMeta as $s => $m) {
        $counts[$s] = (int) ($summary[$s] ?? 0);
    }
    $total    = array_sum($counts);
    $pct      = fn ($n) => $total ? round($n / $total * 100) : 0;
    $verified = $counts['Closed Verified'];
    $waiting  = $counts['Closed'];
    $rate     = $pct($verified);
    $overdueN = $overdue->count();
    $nearN    = $nearDeadline->count();

    // ---------- Tren 12 bulan (bulan kosong diisi 0 agar garis utuh) ----------
    $monthShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $monthFull  = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $trendLabels = $trendFull = $trendData = [];
    for ($i = 11; $i >= 0; $i--) {
        $d = now()->startOfMonth()->subMonths($i);
        $trendLabels[] = $monthShort[$d->month - 1] . ' ' . $d->format('y');
        $trendFull[]   = $monthFull[$d->month - 1] . ' ' . $d->year;
        $trendData[]   = (int) ($trend[$d->format('Y-m')] ?? 0);
    }
    $thisMonth = $trendData[11];
    $prevMonth = $trendData[10];
    $delta     = $thisMonth - $prevMonth;
    $sum12     = array_sum($trendData);
    $avg12     = round($sum12 / 12, 1);
    $peakVal   = max($trendData);
    $peakIdx   = $peakVal > 0 ? array_search($peakVal, $trendData) : null;

    // ---------- Kesimpulan otomatis ----------
    if ($total === 0) {
        $tone = 'neutral'; $toneIcon = 'fa-clipboard';
        $headline = 'Belum ada temuan tercatat';
        $sub = 'Grafik dan ringkasan akan muncul setelah auditor mencatat temuan pertama.';
    } elseif ($overdueN > 0) {
        $tone = 'danger'; $toneIcon = 'fa-triangle-exclamation';
        $headline = $overdueN . ' temuan melewati batas waktu';
        $sub = 'Prioritaskan penanganannya.' . ($nearN > 0 ? " {$nearN} temuan lain mendekati deadline." : ' Belum ada temuan lain yang mendekati deadline.');
    } elseif ($nearN > 0) {
        $tone = 'warn'; $toneIcon = 'fa-hourglass-half';
        $headline = $nearN . ' temuan mendekati deadline';
        $sub = 'Belum ada yang overdue. Tindak lanjuti sebelum tenggat berakhir.';
    } else {
        $tone = 'good'; $toneIcon = 'fa-circle-check';
        $headline = 'Semua temuan terkendali';
        $sub = 'Tidak ada temuan overdue maupun yang mendekati deadline.';
    }

    // "3 hari", "5 jam", "20 menit"
    $span = function ($due) {
        $m = (int) abs(now()->diffInMinutes($due));
        if ($m < 60)   return $m . ' menit';
        if ($m < 1440) return floor($m / 60) . ' jam';
        return floor($m / 1440) . ' hari';
    };
@endphp

@push('styles')
<style>
  /* ===== Dashboard infografik (prefix .kd-) ===== */
  .kd-hero{
    display:grid; grid-template-columns:minmax(0,1.35fr) minmax(0,1fr); gap:28px; align-items:center;
    background:var(--ink); color:#fff; border-radius:var(--radius-lg); padding:32px 34px; position:relative; overflow:hidden;
    border-left:8px solid var(--kd-tone);
  }
  .kd-hero::after{ /* pita hazard tipis di sisi bawah */
    content:""; position:absolute; left:0; right:0; bottom:0; height:5px;
    background:repeating-linear-gradient(135deg, var(--orange) 0 14px, var(--ink) 14px 28px);
  }
  .kd-tone-good{ --kd-tone:#2FB56A; } .kd-tone-warn{ --kd-tone:var(--orange); }
  .kd-tone-danger{ --kd-tone:#FF4A3D; } .kd-tone-neutral{ --kd-tone:#8A958E; }
  .kd-verdict{ display:flex; gap:16px; align-items:flex-start; }
  .kd-verdict-icon{
    flex:0 0 52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    background:var(--kd-tone); color:var(--ink); font-size:22px;
  }
  .kd-tone-danger .kd-verdict-icon{ color:#fff; animation:kd-pulse 1.8s ease-in-out infinite; }
  @keyframes kd-pulse{ 0%,100%{ box-shadow:0 0 0 0 rgba(255,74,61,.55); } 50%{ box-shadow:0 0 0 12px rgba(255,74,61,0); } }
  .kd-headline{ color:#fff; font-family:var(--f-ui); font-weight:800; font-size:30px; line-height:1.15; margin:0 0 8px; letter-spacing:-0.01em; }
  .kd-sub{ color:#C7CFC5; font-size:15px; margin:0; max-width:480px; line-height:1.5; }

  .kd-facts{ display:grid; grid-template-columns:repeat(3,1fr); margin-top:26px; border-top:1px solid rgba(255,255,255,.14); }
  .kd-fact{ padding:16px 16px 0 0; }
  .kd-fact + .kd-fact{ padding-left:16px; border-left:1px solid rgba(255,255,255,.14); }
  .kd-fact b{ display:block; font-family:var(--f-ui); font-size:28px; font-weight:700; line-height:1.1; color:#fff; }
  .kd-fact > span{ display:block; font-size:12.5px; color:#AEB8AF; margin-top:4px; line-height:1.35; }
  .kd-up{ color:#FF8A80; } .kd-down{ color:#7FE0A6; } .kd-flat{ color:#AEB8AF; }

  .kd-donut-wrap{ position:relative; height:270px; }
  .kd-donut-wrap canvas{ cursor:pointer; }

  /* status cards (sekaligus legend donut) */
  .kd-status{
    display:block; text-decoration:none; color:var(--ink); background:var(--panel);
    border:1.5px solid var(--line); border-top:5px solid var(--c); border-radius:var(--radius);
    padding:16px 18px 14px; height:100%; transition:transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease;
  }
  .kd-status:hover,.kd-status:focus-visible,.kd-status.is-active{
    transform:translateY(-4px); border-color:var(--c); background:color-mix(in srgb, var(--c) 6%, #fff);
    box-shadow:0 12px 26px color-mix(in srgb, var(--c) 22%, transparent); color:var(--ink);
  }
  .kd-status-row{ display:flex; align-items:center; justify-content:space-between; gap:10px; }
  .kd-status-name{ font-family:var(--f-ui); font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px; }
  .kd-status-name i{ color:var(--c); }
  .kd-status-num{ font-family:var(--f-ui); font-weight:800; font-size:34px; line-height:1; margin-top:10px; }
  .kd-status-pct{ font-size:13px; color:var(--steel); margin-left:6px; font-weight:500; }
  .kd-status-hint{ font-size:12.5px; color:var(--steel); margin:6px 0 12px; }
  .kd-bar{ height:7px; border-radius:4px; background:var(--paper-deep); overflow:hidden; }
  .kd-bar > i{ display:block; height:100%; width:0; background:var(--c); border-radius:4px; transition:width .9s cubic-bezier(.2,.7,.2,1); }
  .kd-status-go{ opacity:0; transform:translateX(-4px); transition:all .18s ease; color:var(--c); font-size:13px; }
  .kd-status:hover .kd-status-go,.kd-status:focus-visible .kd-status-go,.kd-status.is-active .kd-status-go{ opacity:1; transform:none; }
  .kd-statuses.has-active .kd-status:not(.is-active){ opacity:.55; }
  .kd-statuses .kd-status{ transition:transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease, opacity .18s ease; }

  /* tren */
  .kd-trend-head{ display:flex; flex-wrap:wrap; gap:14px 28px; align-items:flex-end; justify-content:space-between; }
  .kd-mini{ display:flex; gap:26px; flex-wrap:wrap; }
  .kd-mini div{ font-size:12.5px; color:var(--steel); line-height:1.3; }
  .kd-mini b{ display:block; font-family:var(--f-ui); font-size:20px; color:var(--ink); font-weight:700; }
  .kd-trend-box{ position:relative; height:290px; }

  /* daftar deadline */
  .kd-list{ list-style:none; margin:0; padding:0; max-height:340px; overflow:auto; }
  .kd-item{
    display:flex; align-items:center; justify-content:space-between; gap:12px; padding:13px 20px;
    border-bottom:1px solid var(--line); border-left:4px solid transparent; transition:background .15s ease, border-color .15s ease, padding-left .15s ease;
  }
  .kd-item:last-child{ border-bottom:none; }
  .kd-item:hover{ padding-left:24px; }
  .kd-item--late:hover{ background:var(--red-pale); border-left-color:var(--red); }
  .kd-item--near:hover{ background:var(--orange-pale); border-left-color:var(--orange); }
  .kd-item-no{ font-family:var(--f-mono); font-weight:700; font-size:15px; color:var(--ink); }
  .kd-item-meta{ font-size:12.5px; color:var(--steel); display:block; margin-top:2px; }
  .kd-chip{
    flex-shrink:0; font-family:var(--f-ui); font-weight:600; font-size:12.5px; padding:6px 12px; border-radius:20px; white-space:nowrap;
    display:inline-flex; align-items:center; gap:6px;
  }
  .kd-chip--late{ background:var(--red); color:#fff; }
  .kd-chip--near{ background:var(--orange); color:var(--ink); }
  .kd-empty{ padding:34px 20px; text-align:center; color:var(--steel); font-size:14px; }
  .kd-empty i{ display:block; font-size:24px; margin-bottom:8px; color:var(--green); }
  .kd-more{ font-family:var(--f-ui); font-weight:600; font-size:13px; text-decoration:none; margin-left:auto; }

  @media (max-width:991.98px){
    .kd-hero{ grid-template-columns:1fr; padding:26px 22px; }
    .kd-donut-wrap{ height:240px; }
  }
  @media (max-width:575.98px){
    .kd-headline{ font-size:24px; }
    .kd-facts{ grid-template-columns:1fr; }
    .kd-fact + .kd-fact{ padding-left:0; border-left:none; border-top:1px solid rgba(255,255,255,.14); margin-top:14px; }
    .kd-fact b{ font-size:24px; }
  }
  @media (max-width:767.98px){
    .kd-hero{ padding:22px 18px; gap:18px; }
    .kd-verdict{ gap:12px; }
    .kd-verdict-icon{ flex-basis:44px; height:44px; font-size:18px; }
    .kd-donut-wrap{ height:220px; }
    .kd-status{ padding:14px 14px 12px; }
    .kd-status-name{ font-size:13px; }
    .kd-status-num{ font-size:28px; margin-top:8px; }
    .kd-status-hint{ font-size:12px; margin:4px 0 10px; }
    .kd-trend-box{ height:240px; }
    .kd-mini{ gap:12px 22px; }
    .kd-list{ max-height:300px; }
    .kd-item{ flex-wrap:wrap; padding:12px 16px; }
    .kd-item:hover{ padding-left:20px; }
  }
  @media (max-width:575.98px){
    .kd-item > div{ flex:1 1 100%; }
    .kd-item-no{ font-size:14px; }
    .kd-trend-head h3{ font-size:1.05rem; }
  }
</style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Ringkasan Sistem</div>
            <h1 class="page-title">Dashboard K3 Patrol</h1>
            <p class="page-subtitle">Pantau status temuan, tren bulanan, dan tenggat perbaikan dari satu tempat.</p>
        </div>
    </div>

    {{-- ===== 1. Kesimpulan + donut ===== --}}
    <section class="kd-hero kd-tone-{{ $tone }} mb-4" aria-label="Kesimpulan">
        <div>
            <div class="kd-verdict">
                <div class="kd-verdict-icon"><i class="fa-solid {{ $toneIcon }}"></i></div>
                <div>
                    <h2 class="kd-headline">{{ $headline }}</h2>
                    <p class="kd-sub">{{ $sub }}</p>
                </div>
            </div>

            <div class="kd-facts">
                <div class="kd-fact">
                    <b><i data-count="{{ $rate }}" style="font-style:normal">0</i>%</b>
                    <span>temuan sudah terverifikasi ({{ $verified }} dari {{ $total }})</span>
                </div>
                <div class="kd-fact">
                    <b data-count="{{ $waiting }}">0</b>
                    <span>temuan sudah ditutup, menunggu verifikasi auditor</span>
                </div>
                <div class="kd-fact">
                    <b>
                        <i data-count="{{ $thisMonth }}" style="font-style:normal">0</i>
                        @if ($delta > 0)
                            <small class="kd-up" style="font-size:14px"><i class="fa-solid fa-arrow-trend-up"></i> +{{ $delta }}</small>
                        @elseif ($delta < 0)
                            <small class="kd-down" style="font-size:14px"><i class="fa-solid fa-arrow-trend-down"></i> {{ $delta }}</small>
                        @else
                            <small class="kd-flat" style="font-size:14px"><i class="fa-solid fa-minus"></i> sama</small>
                        @endif
                    </b>
                    <span>temuan baru bulan ini, dibanding {{ $prevMonth }} bulan lalu</span>
                </div>
            </div>
        </div>

        <div class="kd-donut-wrap">
            <canvas id="statusChart" role="img"
                aria-label="Diagram donat status temuan: {{ collect($counts)->map(fn ($n, $s) => "$s $n")->implode(', ') }}"></canvas>
        </div>
    </section>

    {{-- ===== 2. Kartu status (legend donut, klik untuk filter) ===== --}}
    <div class="row g-3 mb-4 kd-statuses" id="statusCards">
        @foreach ($statusMeta as $s => $m)
            <div class="col-6 col-lg-3">
                <a href="{{ route('findings.index', ['status' => $s]) }}" class="kd-status" data-idx="{{ $loop->index }}" style="--c:{{ $m['color'] }}"
                   title="Lihat temuan berstatus {{ $s }}">
                    <div class="kd-status-row">
                        <span class="kd-status-name"><i class="fa-solid {{ $m['icon'] }}"></i>{{ $s }}</span>
                        <i class="fa-solid fa-arrow-right kd-status-go"></i>
                    </div>
                    <div class="kd-status-num"><span data-count="{{ $counts[$s] }}">0</span><span class="kd-status-pct">{{ $pct($counts[$s]) }}%</span></div>
                    <div class="kd-status-hint">{{ $m['hint'] }}</div>
                    <div class="kd-bar"><i data-w="{{ $pct($counts[$s]) }}"></i></div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- ===== 3. Tren ===== --}}
    <div class="card mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="kd-trend-head mb-3">
                <div>
                    <h3 class="h5 mb-1">Tren temuan 12 bulan terakhir</h3>
                    <div class="text-muted small">Arahkan kursor ke titik untuk melihat jumlah dan selisih dari bulan sebelumnya.</div>
                </div>
                <div class="kd-mini">
                    <div><b>{{ $sum12 }}</b>total 12 bulan</div>
                    <div><b>{{ $avg12 }}</b>rata-rata per bulan</div>
                    <div><b>{{ $peakIdx !== null ? $trendLabels[$peakIdx] : '-' }}</b>bulan tertinggi{{ $peakIdx !== null ? " ({$peakVal})" : '' }}</div>
                </div>
            </div>
            <div class="kd-trend-box"><canvas id="trendChart" role="img" aria-label="Grafik garis jumlah temuan per bulan selama 12 bulan terakhir"></canvas></div>
        </div>
    </div>

    {{-- ===== 4. Deadline ===== --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-danger h-100 k3-hazard-edge">
                <div class="card-header">
                    <i class="fa-solid fa-triangle-exclamation"></i> Overdue ({{ $overdueN }})
                    <a href="{{ route('findings.index') }}" class="kd-more">Semua temuan</a>
                </div>
                <ul class="kd-list">
                    @forelse ($overdue as $f)
                        <li class="kd-item kd-item--late">
                            <div>
                                <span class="kd-item-no">{{ $f->finding_number }}</span>
                                <span class="kd-item-meta">{{ $f->department->name }} &middot; due {{ $f->due_at->format('d M Y H:i') }}</span>
                            </div>
                            <span class="kd-chip kd-chip--late"><i class="fa-solid fa-clock"></i>Terlambat {{ $span($f->due_at) }}</span>
                        </li>
                    @empty
                        <li class="kd-empty"><i class="fa-solid fa-champagne-glasses"></i>Tidak ada temuan overdue.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-warning h-100">
                <div class="card-header">
                    <i class="fa-regular fa-clock"></i> Mendekati deadline ({{ $nearN }})
                    <a href="{{ route('findings.index') }}" class="kd-more">Semua temuan</a>
                </div>
                <ul class="kd-list">
                    @forelse ($nearDeadline as $f)
                        <li class="kd-item kd-item--near">
                            <div>
                                <span class="kd-item-no">{{ $f->finding_number }}</span>
                                <span class="kd-item-meta">{{ $f->department->name }} &middot; due {{ $f->due_at->format('d M Y H:i') }}</span>
                            </div>
                            <span class="kd-chip kd-chip--near"><i class="fa-solid fa-hourglass-half"></i>Sisa {{ $span($f->due_at) }}</span>
                        </li>
                    @empty
                        <li class="kd-empty"><i class="fa-solid fa-mug-hot"></i>Tidak ada temuan mendekati deadline.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
(function () {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fontUi = "'Rubik', sans-serif";
    Chart.defaults.font.family = "'Roboto', sans-serif";
    Chart.defaults.color = '#5B665F';

    // ---------- angka naik + bar terisi ----------
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10) || 0;
        if (reduceMotion || target === 0) { el.textContent = target; return; }
        const t0 = performance.now(), dur = 900;
        const tick = now => {
            const p = Math.min((now - t0) / dur, 1);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });
    requestAnimationFrame(() => document.querySelectorAll('.kd-bar > i').forEach(b => b.style.width = b.dataset.w + '%'));

    // ---------- donut status ----------
    const labels = @json(array_keys($counts));
    const values = @json(array_values($counts));
    const colors = @json(array_column($statusMeta, 'color'));
    const total  = values.reduce((a, b) => a + b, 0);
    const empty  = total === 0;
    const cards  = document.querySelectorAll('.kd-status');
    const wrap   = document.getElementById('statusCards');

    function highlight(idx) {
        cards.forEach(c => c.classList.toggle('is-active', idx !== null && +c.dataset.idx === idx));
        wrap.classList.toggle('has-active', idx !== null);
    }

    const centerText = {
        id: 'centerText',
        afterDraw(chart) {
            const a = chart.chartArea, x = (a.left + a.right) / 2, y = (a.top + a.bottom) / 2;
            const act = chart.getActiveElements();
            const i = act.length ? act[0].index : null;
            const big = empty ? '0' : (i !== null ? values[i] : total);
            const small = empty ? 'temuan' : (i !== null ? labels[i] : 'total temuan');
            const ctx = chart.ctx;
            ctx.save();
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.fillStyle = '#fff'; ctx.font = `800 40px ${fontUi}`;
            ctx.fillText(big, x, y - 10);
            ctx.fillStyle = i !== null ? colors[i] : '#AEB8AF'; ctx.font = `600 13px ${fontUi}`;
            ctx.fillText(small, x, y + 20);
            ctx.restore();
        }
    };

    const donut = new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: empty ? ['Belum ada data'] : labels,
            datasets: [{
                data: empty ? [1] : values,
                backgroundColor: empty ? ['#2C3A33'] : colors,
                borderColor: '#16211C', borderWidth: 4, hoverOffset: 12, spacing: 1
            }]
        },
        options: {
            maintainAspectRatio: false, cutout: '68%',
            animation: reduceMotion ? false : { duration: 900 },
            layout: { padding: 12 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: !empty, backgroundColor: '#fff', titleColor: '#16211C', bodyColor: '#3B453F',
                    borderColor: '#F2A20C', borderWidth: 2, padding: 12, cornerRadius: 6, displayColors: true, boxPadding: 4,
                    titleFont: { family: fontUi, weight: '600', size: 14 },
                    callbacks: {
                        label: c => ` ${c.parsed} temuan (${Math.round(c.parsed / total * 100)}%)`,
                        afterLabel: c => ' ' + @json(array_column($statusMeta, 'hint'))[c.dataIndex]
                    }
                }
            },
            onHover(evt, els) { if (!empty) highlight(els.length ? els[0].index : null); },
            onClick(evt, els) { if (els.length && cards[els[0].index]) window.location = cards[els[0].index].href; }
        },
        plugins: [centerText]
    });

    // kartu -> donut
    cards.forEach(card => {
        const i = +card.dataset.idx;
        const on = () => {
            if (empty) return;
            const arc = donut.getDatasetMeta(0).data[i];
            const pos = arc.tooltipPosition();
            donut.setActiveElements([{ datasetIndex: 0, index: i }]);
            donut.tooltip.setActiveElements([{ datasetIndex: 0, index: i }], { x: pos.x, y: pos.y });
            donut.update();
            highlight(i);
        };
        const off = () => {
            donut.setActiveElements([]);
            donut.tooltip.setActiveElements([], { x: 0, y: 0 });
            donut.update();
            highlight(null);
        };
        card.addEventListener('mouseenter', on); card.addEventListener('mouseleave', off);
        card.addEventListener('focus', on);      card.addEventListener('blur', off);
    });

    // ---------- tren ----------
    const tLabels = @json($trendLabels), tFull = @json($trendFull), tData = @json($trendData);
    const peak = tData.indexOf(Math.max(...tData)), last = tData.length - 1, hasPeak = Math.max(...tData) > 0;
    const tctx = document.getElementById('trendChart').getContext('2d');
    const grad = tctx.createLinearGradient(0, 0, 0, 290);
    grad.addColorStop(0, 'rgba(242,162,12,.38)'); grad.addColorStop(1, 'rgba(242,162,12,0)');

    new Chart(tctx, {
        type: 'line',
        data: {
            labels: tLabels,
            datasets: [{
                label: 'Temuan', data: tData, fill: true, tension: .35,
                borderColor: '#16211C', borderWidth: 2.5, backgroundColor: grad,
                pointBackgroundColor: tData.map((_, i) => i === last ? '#16211C' : (hasPeak && i === peak ? '#C57F00' : '#fff')),
                pointBorderColor: tData.map((_, i) => hasPeak && i === peak && i !== last ? '#C57F00' : '#16211C'),
                pointBorderWidth: 2,
                pointRadius: tData.map((_, i) => (i === last || (hasPeak && i === peak)) ? 6 : 3.5),
                pointHoverRadius: 9, pointHoverBackgroundColor: '#F2A20C', pointHoverBorderColor: '#16211C', pointHoverBorderWidth: 3
            }]
        },
        options: {
            maintainAspectRatio: false,
            animation: reduceMotion ? false : { duration: 900 },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#16211C', titleColor: '#F2A20C', bodyColor: '#fff', padding: 12, cornerRadius: 6, displayColors: false,
                    titleFont: { family: fontUi, weight: '600', size: 13 }, bodyFont: { size: 14 },
                    callbacks: {
                        title: items => tFull[items[0].dataIndex],
                        label: c => `${c.parsed.y} temuan`,
                        afterLabel: c => {
                            if (c.dataIndex === 0) return '';
                            const d = c.parsed.y - tData[c.dataIndex - 1];
                            return d === 0 ? 'Sama dengan bulan sebelumnya' : (d > 0 ? `Naik ${d} dari bulan sebelumnya` : `Turun ${Math.abs(d)} dari bulan sebelumnya`);
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 12 }, maxRotation: 0, autoSkip: true, maxTicksLimit: window.innerWidth < 576 ? 6 : 12 } },
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 12 } }, grid: { color: '#E2E5D6', borderDash: [4, 4] }, border: { display: false } }
            }
        }
    });
})();
</script>
@endpush