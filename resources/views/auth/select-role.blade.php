<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#16211C">
<title>Pilih Peran - K3 Patrol</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&family=Roboto:wght@400;500;700&family=Rubik:wght@400;500;600;700;800&family=Ubuntu+Mono:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/k3-theme.css') }}" rel="stylesheet">
<style>
  /* ---------- halaman: grid blueprint + siluet proyek di dasar ---------- */
  body{
    min-height:100vh; min-height:100dvh;
    background:
      linear-gradient(var(--line) 1px, transparent 1px),
      linear-gradient(90deg, var(--line) 1px, transparent 1px),
      var(--paper);
    background-size:42px 42px, 42px 42px, auto;
  }
  .site{
    position:fixed; left:0; right:0; bottom:0; z-index:0; width:100%;
    height:30vh; min-height:140px; max-height:260px; pointer-events:none;
  }
  .hook{ transform-box:fill-box; transform-origin:50% 0; animation:hook-swing 5s ease-in-out infinite alternate; }
  @keyframes hook-swing{ from{ transform:rotate(-3.5deg); } to{ transform:rotate(3.5deg); } }

  .gate{
    position:relative; z-index:1; display:flex; align-items:center; justify-content:center;
    min-height:calc(100vh - 5px); min-height:calc(100dvh - 5px);
    padding:28px 24px calc(36px + env(safe-area-inset-bottom, 0px));
  }
  .role-shell{ width:100%; max-width:960px; }

  /* ---------- papan nama + judul ---------- */
  .role-brand{ display:flex; justify-content:center; margin-bottom:26px; }
  .role-sign{
    display:inline-flex; align-items:center; gap:10px; padding:8px 18px 8px 12px;
    background:var(--ink); border-radius:8px; box-shadow:0 3px 0 var(--orange), var(--shadow);
  }
  .role-sign img{ width:30px; height:30px; }
  .role-sign b{ font-family:var(--f-display); font-size:18px; letter-spacing:.04em; color:var(--paper); }
  .role-head{ text-align:center; margin-bottom:34px; }
  .role-head .page-eyebrow{ justify-content:center; }
  .role-head h1{ font-size:clamp(24px, 4.2vw, 36px); line-height:1.2; margin:6px 0 8px; }
  .role-head p{ margin:0 auto; max-width:440px; color:var(--steel); font-size:15px; line-height:1.5; }

  /* ---------- kartu akses (satu kartu = satu peran) ---------- */
  .role-options{
    --cols:3; display:grid; grid-template-columns:repeat(var(--cols), minmax(0, 1fr)); gap:20px;
    width:100%; max-width:calc(var(--cols) * 300px); margin:0 auto;
  }
  .pass{
    --role:var(--ink); --role-pale:var(--steel-pale); --tx:0; --ty:0;
    position:relative; display:flex; flex-direction:column; width:100%; padding:0; overflow:hidden;
    font:inherit; color:var(--ink); text-align:center; cursor:pointer;
    background:var(--panel); border:1.5px solid var(--line); border-radius:14px; box-shadow:var(--shadow);
    transform:perspective(900px) rotateX(calc(var(--ty) * -7deg)) rotateY(calc(var(--tx) * 7deg));
    transition:transform .18s ease-out, box-shadow .2s ease, border-color .2s ease, opacity .25s ease, filter .25s ease;
    animation:pass-drop .65s cubic-bezier(.2,.9,.3,1.15) var(--d, 0s) backwards;
    -webkit-tap-highlight-color:transparent;
  }
  .pass-admin{ --role:var(--ink); --role-pale:var(--steel-pale); }
  .pass-auditor{ --role:var(--red); --role-pale:var(--red-pale); }
  .pass-auditee{ --role:var(--green); --role-pale:var(--green-pale); }
  @keyframes pass-drop{ from{ opacity:0; transform:translateY(-28px) rotate(-2.5deg); } }

  .pass:hover, .pass:focus-visible{
    border-color:var(--role); box-shadow:var(--shadow-lg);
    transform:perspective(900px) translateY(-8px) rotateX(calc(var(--ty) * -7deg)) rotateY(calc(var(--tx) * 7deg));
  }
  .pass:focus-visible{ outline:3px solid var(--orange); outline-offset:3px; }
  .pass:active{ transform:perspective(900px) translateY(-2px) scale(.985); }

  /* bagian atas: pita hazard, lubang tali, helm */
  .pass-top{
    position:relative; display:flex; align-items:center; justify-content:center;
    padding:38px 16px 26px; background:var(--role-pale);
  }
  .pass-top::before{
    content:""; position:absolute; left:0; right:0; top:0; height:8px;
    background:repeating-linear-gradient(135deg, var(--orange) 0 10px, var(--ink) 10px 20px);
  }
  .pass-top::after{
    content:""; position:absolute; left:50%; bottom:14px; width:96px; height:12px; margin-left:-48px;
    border-radius:50%; background:rgba(22,33,28,.16); filter:blur(3px);
    transition:transform .25s ease, opacity .25s ease;
  }
  .pass-slot{
    position:absolute; top:18px; left:50%; width:46px; height:10px; margin-left:-23px;
    border-radius:6px; background:var(--paper); box-shadow:inset 0 2px 3px rgba(22,33,28,.28);
  }
  .pass-hat{
    position:relative; z-index:1; width:128px; height:auto; color:var(--role); overflow:visible;
    transform:translateX(calc(var(--tx) * 12px)) rotate(calc(var(--tx) * 10deg));
    transition:transform .3s cubic-bezier(.3,1.5,.5,1);
  }
  .pass:hover .pass-hat, .pass:focus-visible .pass-hat{
    transform:translateY(-8px) translateX(calc(var(--tx) * 12px)) rotate(calc(var(--tx) * 10deg));
  }
  .pass:hover .pass-top::after, .pass:focus-visible .pass-top::after{ transform:scale(.78); opacity:.6; }

  /* isi */
  .pass-body{ display:block; flex:1; padding:18px 16px; }
  .pass-name{ display:block; font-family:var(--f-display); font-weight:700; font-size:22px; line-height:1.2; letter-spacing:.01em; }
  .pass-desc{ display:block; margin-top:6px; font-size:13.5px; line-height:1.45; color:var(--steel); }

  /* bawah: garis sobek + tombol masuk */
  .pass-go{
    position:relative; display:flex; align-items:center; justify-content:center; gap:10px;
    padding:13px 16px; border-top:2px dashed var(--line);
    font-family:var(--f-ui); font-weight:600; font-size:13.5px; color:var(--role);
    transition:background .2s ease, color .2s ease, border-color .2s ease;
  }
  .pass-go::before, .pass-go::after{
    content:""; position:absolute; top:-8px; width:14px; height:14px; border-radius:50%;
    background:var(--paper); border:1.5px solid var(--line);
  }
  .pass-go::before{ left:-8px; }
  .pass-go::after{ right:-8px; }
  .pass-go svg{ width:18px; height:18px; flex:none; transition:transform .2s ease; }
  .pass:hover .pass-go, .pass:focus-visible .pass-go{ background:var(--role); border-top-color:var(--role); color:#fff; }
  .pass:hover .pass-go svg, .pass:focus-visible .pass-go svg{ transform:translateX(4px); }

  /* ---------- status setelah dipilih ---------- */
  .is-sending .pass{ pointer-events:none; }
  .is-sending .pass:not(.is-chosen){ opacity:.4; filter:grayscale(.7); transform:none; }
  .pass.is-chosen{ border-color:var(--role); box-shadow:var(--shadow-lg); }
  .pass.is-chosen .pass-go{ background:var(--role); border-top-color:var(--role); color:#fff; }
  .pass.is-chosen .pass-top::before{
    background:linear-gradient(135deg, var(--orange) 25%, var(--ink) 25% 50%, var(--orange) 50% 75%, var(--ink) 75%);
    background-size:20px 20px; animation:hazard-run .5s linear infinite;
  }
  .pass.is-chosen .pass-hat{ animation:hat-bob .55s ease-in-out infinite alternate; }
  .pass.is-chosen .pass-go svg{ animation:go-nudge .55s ease-in-out infinite alternate; }
  @keyframes hazard-run{ to{ background-position:20px 0; } }
  @keyframes hat-bob{ to{ transform:translateY(-8px); } }
  @keyframes go-nudge{ to{ transform:translateX(5px); } }

  .role-empty{
    max-width:420px; margin:0 auto; padding:26px 22px; text-align:center;
    background:var(--panel); border:2px dashed var(--line); border-radius:14px;
  }
  .role-empty b{ display:block; font-family:var(--f-display); font-size:18px; margin-bottom:6px; }
  .role-empty span{ color:var(--steel); font-size:14px; }
  .role-note{ margin:28px 0 0; text-align:center; font-size:13px; color:var(--steel); }

  /* ---------- responsif ---------- */
  @media (min-width:768px) and (max-width:991.98px){
    .role-options{ gap:14px; }
    .pass-hat{ width:108px; }
    .pass-name{ font-size:20px; }
    .pass-go{ padding-left:10px; padding-right:10px; font-size:13px; }
  }
  @media (max-width:767.98px){
    .gate{ padding:20px 16px calc(28px + env(safe-area-inset-bottom, 0px)); }
    .role-brand{ margin-bottom:20px; }
    .role-head{ margin-bottom:24px; }
    .role-head p{ font-size:14px; }
    .role-options{ grid-template-columns:1fr; gap:12px; max-width:480px; }
    .pass{ display:grid; grid-template-columns:96px minmax(0, 1fr) auto; align-items:stretch; text-align:left; }
    .pass:hover, .pass:focus-visible{ transform:none; }
    .pass:active{ transform:scale(.985); }
    .pass-top{ padding:12px 6px 12px 16px; }
    .pass-top::before{ right:auto; bottom:0; width:8px; height:auto; }
    .pass-top::after, .pass-slot{ display:none; }
    .pass-hat{ width:68px; }
    .pass-body{ align-self:center; padding:12px 8px 12px 14px; }
    .pass-name{ font-size:18px; }
    .pass-desc{ font-size:13px; margin-top:3px; }
    .pass-go{ padding:0 14px; border-top:0; border-left:2px dashed var(--line); }
    .pass-go::before, .pass-go::after{ display:none; }
    .pass-go .go-text{ position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0 0 0 0); white-space:nowrap; }
    .pass:hover .pass-go, .pass:focus-visible .pass-go{ border-left-color:var(--role); }
    .pass.is-chosen .pass-go{ border-left-color:var(--role); }
    .role-note{ margin-top:22px; }
  }
  @media (max-width:359.98px){
    .pass{ grid-template-columns:76px minmax(0, 1fr) auto; }
    .pass-hat{ width:56px; }
    .pass-top{ padding-left:12px; }
    .pass-name{ font-size:17px; }
  }
  @media (prefers-reduced-motion: reduce){
    .hook, .pass, .pass.is-chosen .pass-top::before, .pass.is-chosen .pass-hat, .pass.is-chosen .pass-go svg{ animation:none !important; }
  }
</style>
</head>
<body>
@php
  $available = auth()->user()->availableRoles();
  $roleMap = [
    'admin'   => ['label' => 'Admin',   'desc' => 'Kelola master data & user'],
    'auditor' => ['label' => 'Auditor', 'desc' => 'Lapor & verifikasi temuan'],
    'auditee' => ['label' => 'Auditee', 'desc' => 'Tindak lanjuti perbaikan'],
  ];
  $items    = array_intersect_key($roleMap, array_flip($available));
  $userName = data_get(auth()->user(), 'name');
@endphp

<div class="hazard-rail"></div>

{{-- sprite: helm proyek + ikon peran --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <symbol id="k3-hat" viewBox="0 0 120 96">
    <rect x="6" y="66" width="108" height="14" rx="7" fill="currentColor"/>
    <rect x="6" y="66" width="108" height="14" rx="7" fill="#000" opacity=".22"/>
    <path d="M18 70C18 36 36 14 60 14s42 22 42 56z" fill="currentColor"/>
    <path d="M51 17c0-4 18-4 18 0v53H51z" fill="#fff" opacity=".16"/>
    <path d="M51 17v53M69 17v53" stroke="#000" stroke-opacity=".18" stroke-width="2"/>
    <path d="M27 58c1-16 9-28 22-34" fill="none" stroke="#fff" stroke-opacity=".35" stroke-width="5" stroke-linecap="round"/>
    <rect x="18" y="66" width="84" height="6" fill="#000" opacity=".18"/>
  </symbol>
  <symbol id="k3-ico-admin" viewBox="-12 -12 24 24">
    <g fill="none" stroke="currentColor">
      <circle r="4.6" stroke-width="2.4"/>
      <circle r="7.4" stroke-width="3.2" stroke-dasharray="2.9 2.91"/>
    </g>
  </symbol>
  <symbol id="k3-ico-auditor" viewBox="-12 -12 24 24">
    <g fill="none" stroke="currentColor" stroke-linecap="round">
      <circle cx="-2" cy="-2" r="5.6" stroke-width="2.4"/>
      <path d="M2.3 2.3L8 8" stroke-width="3"/>
    </g>
  </symbol>
  <symbol id="k3-ico-auditee" viewBox="-12 -12 24 24">
    <path d="M-6.5 .5L-2 5L6.5 -4.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
  </symbol>
</svg>

{{-- siluet proyek: rangka gedung + tower crane --}}
<svg class="site" viewBox="0 0 1200 240" preserveAspectRatio="xMidYMax slice" aria-hidden="true" focusable="false">
  <defs>
    <pattern id="k3Frame" width="48" height="28" patternUnits="userSpaceOnUse">
      <path d="M3 0V28M0 26H48" fill="none" stroke="currentColor" stroke-width="4"/>
    </pattern>
    <pattern id="k3Lat" width="16" height="16" patternUnits="userSpaceOnUse">
      <path d="M0 0L16 16M16 0L0 16" fill="none" stroke="currentColor" stroke-width="1.8"/>
    </pattern>
  </defs>
  <g fill="currentColor" style="color:var(--ink)" opacity=".1">
    <rect x="48" y="80" width="198" height="146" fill="url(#k3Frame)"/>
    <rect x="960" y="54" width="198" height="172" fill="url(#k3Frame)"/>
    <rect x="0" y="224" width="1200" height="16"/>
  </g>
  <g fill="currentColor" style="color:var(--orange-deep)" opacity=".3">
    <rect x="700" y="40" width="16" height="186" fill="url(#k3Lat)" stroke="currentColor" stroke-width="3"/>
    <rect x="620" y="34" width="390" height="12" fill="url(#k3Lat)" stroke="currentColor" stroke-width="3"/>
    <rect x="620" y="46" width="26" height="22"/>
    <rect x="718" y="46" width="18" height="14"/>
    <path d="M698 34L707 6L716 34M707 6L1010 36M707 6L620 36" fill="none" stroke="currentColor" stroke-width="1.6"/>
    <rect x="872" y="46" width="16" height="6"/>
    <g class="hook">
      <path d="M880 52V120M880 130L852 136M880 130L908 136" fill="none" stroke="currentColor" stroke-width="1.6"/>
      <rect x="872" y="120" width="16" height="10"/>
      <rect x="848" y="136" width="64" height="9"/>
    </g>
  </g>
</svg>

<main class="gate">
  <div class="role-shell">
    <div class="role-brand">
      <div class="role-sign">
        <img src="{{ asset('images/icon_k3.png') }}" alt="">
        <b>K3 PATROL</b>
      </div>
    </div>

    <header class="role-head">
      <div class="page-eyebrow">Satu Akun, Beberapa Peran</div>
      <h1>Masuk sebagai peran apa?</h1>
      <p>@if ($userName)Halo, {{ $userName }}. @endif Pilih peran sesuai tugas Anda hari ini.</p>
    </header>

    @if (count($items))
      <form id="roleForm" method="POST" action="{{ route('select-role.submit') }}" class="role-options"
            style="--cols: {{ count($items) }}" role="group" aria-label="Pilih peran">
        @csrf
        @foreach ($items as $key => $r)
          <button type="submit" name="role" value="{{ $key }}" class="pass pass-{{ $key }}"
                  style="--d: {{ $loop->index * 0.09 }}s" data-label="{{ $r['label'] }}">
            <span class="pass-top">
              <span class="pass-slot"></span>
              <svg class="pass-hat" viewBox="0 0 120 96" aria-hidden="true" focusable="false">
                <use href="#k3-hat"/>
                <circle cx="60" cy="46" r="13" fill="#fff" stroke="currentColor" stroke-width="2"/>
                <use href="#k3-ico-{{ $key }}" x="48" y="34" width="24" height="24"/>
              </svg>
            </span>
            <span class="pass-body">
              <strong class="pass-name">{{ $r['label'] }}</strong>
              <span class="pass-desc">{{ $r['desc'] }}</span>
            </span>
            <span class="pass-go">
              <span class="go-text">Masuk sebagai {{ $r['label'] }}</span>
              <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M7 4l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
          </button>
        @endforeach
      </form>
    @else
      <div class="role-empty">
        <b>Akun Anda belum punya peran</b>
        <span>Hubungi admin untuk meminta akses.</span>
      </div>
    @endif

    <p class="role-note">Selamat bertugas, utamakan keselamatan.</p>
    <p class="visually-hidden" id="roleStatus" role="status" aria-live="polite"></p>
  </div>
</main>

<script>
(function () {
  var form = document.getElementById('roleForm');
  if (!form) return;
  var status = document.getElementById('roleStatus');
  var sending = false;

  /* Tandai kartu terpilih. Tombol TIDAK di-disable agar name/value "role" tetap terkirim;
     klik ganda dicegah lewat flag + pointer-events. */
  form.addEventListener('submit', function (e) {
    if (sending) { e.preventDefault(); return; }
    var btn = e.submitter || document.activeElement;
    if (!btn || !btn.classList || !btn.classList.contains('pass')) return;
    var go = btn.querySelector('.go-text');
    sending = true;
    go.setAttribute('data-orig', go.textContent);
    go.textContent = 'Menyiapkan area kerja…';
    form.classList.add('is-sending');
    btn.classList.add('is-chosen');
    status.textContent = 'Masuk sebagai ' + btn.getAttribute('data-label') + '…';
  });

  /* Tombol Back (bfcache): kembalikan tampilan awal */
  window.addEventListener('pageshow', function (e) {
    if (!e.persisted) return;
    sending = false;
    form.classList.remove('is-sending');
    status.textContent = '';
    Array.prototype.forEach.call(form.querySelectorAll('.is-chosen'), function (btn) {
      var go = btn.querySelector('.go-text');
      go.textContent = go.getAttribute('data-orig') || go.textContent;
      btn.classList.remove('is-chosen');
    });
  });

  /* Kartu miring + helm mengikuti kursor (hanya mouse, bukan layar sentuh) */
  var fine   = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!fine || reduce) return;
  Array.prototype.forEach.call(form.querySelectorAll('.pass'), function (card) {
    card.addEventListener('pointermove', function (e) {
      var r = card.getBoundingClientRect();
      card.style.setProperty('--tx', ((e.clientX - r.left) / r.width - 0.5).toFixed(3));
      card.style.setProperty('--ty', ((e.clientY - r.top) / r.height - 0.5).toFixed(3));
    });
    card.addEventListener('pointerleave', function () {
      card.style.setProperty('--tx', 0);
      card.style.setProperty('--ty', 0);
    });
  });
})();
</script>
</body>
</html>