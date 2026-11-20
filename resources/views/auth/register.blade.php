<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#16211C">
<title>Daftar Akun - K3 Patrol</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&family=Roboto:wght@400;500;700&family=Rubik:wght@400;500;600;700;800&family=Ubuntu+Mono:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
<link href="{{ asset('css/k3-theme.css') }}" rel="stylesheet">
<style>
  body{
    min-height:100vh; min-height:100dvh;
    background:
      linear-gradient(var(--line) 1px, transparent 1px),
      linear-gradient(90deg, var(--line) 1px, transparent 1px),
      var(--paper);
    background-size:42px 42px, 42px 42px, auto;
  }
  .reg-shell{ max-width:600px; margin:0 auto; padding:28px 16px 40px; }
  .reg-brand{ display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:22px; color:var(--ink); text-decoration:none; }
  .reg-brand img{ width:36px; height:36px; }
  .reg-brand b{ font-family:var(--f-display); font-size:20px; letter-spacing:0.03em; }
  .reg-head{ text-align:center; margin-bottom:22px; }
  .reg-head .page-eyebrow{ justify-content:center; }
  .reg-head h1{ font-size:26px; margin:6px 0 6px; font-weight:800; }
  .reg-head p{ color:var(--steel); font-size:14px; margin:0 auto; max-width:440px; }
  .reg-card{ position:relative; overflow:hidden; border-top:0; box-shadow:var(--shadow-lg); }
  .reg-card::before{
    content:""; position:absolute; top:0; left:0; right:0; height:5px;
    background:repeating-linear-gradient(45deg, var(--orange) 0 14px, var(--ink) 14px 28px);
  }
  .reg-card .card-body{ padding:30px 28px 26px; }
  .reg-role{
    display:flex; align-items:flex-start; gap:12px; height:100%; cursor:pointer;
    border:1.5px solid var(--line); border-radius:var(--radius-sm); padding:12px 14px; background:var(--panel);
    transition:border-color .15s ease, background .15s ease, box-shadow .15s ease;
  }
  .reg-role:hover{ border-color:var(--green); }
  .reg-role:has(input:checked){ border-color:var(--green); background:var(--green-pale); box-shadow:0 0 0 2px rgba(14,107,59,.18); }
  .reg-role .form-check-input{ margin-top:3px; flex-shrink:0; }
  .reg-role strong{ display:block; font-family:var(--f-ui); font-size:14.5px; }
  .reg-role small{ display:block; color:var(--steel); font-size:12.5px; line-height:1.4; }
  .reg-dept{
    display:flex; align-items:center; gap:10px; cursor:pointer; height:100%;
    border:1.5px solid var(--line); border-radius:var(--radius-sm); padding:9px 12px; background:var(--panel); font-size:14px;
  }
  .reg-dept:has(input:checked){ border-color:var(--green); background:var(--green-pale); }
  .reg-dept .form-check-input{ margin:0; flex-shrink:0; }
  .reg-note{ font-size:12.5px; color:var(--steel); display:flex; gap:7px; align-items:flex-start; margin-top:8px; line-height:1.45; }
  .reg-note i{ margin-top:2px; }
  .reg-foot{ text-align:center; margin-top:18px; font-size:14px; color:var(--ink-soft); }
  .reg-foot a{ font-weight:600; }
  .reg-card .input-group .btn{ text-transform:none; letter-spacing:0; }
  @media (max-width:575.98px){
    .reg-shell{ padding:20px 14px 32px; }
    .reg-card .card-body{ padding:24px 18px 20px; }
    .reg-head h1{ font-size:22px; }
  }
</style>
</head>
<body>
  <main class="reg-shell">
    <a href="{{ route('login') }}" class="reg-brand">
      <img src="{{ asset('images/icon_k3.png') }}" alt="K3 Patrol">
      <b>K3 PATROL</b>
    </a>

    <div class="reg-head">
      <div class="page-eyebrow">Akun Baru</div>
      <h1>Daftar Akun</h1>
      <p>Isi data berikut untuk membuat akun Auditor atau Auditee.</p>
    </div>

    <div class="card reg-card">
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger" role="alert">
            <strong class="d-block mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i>Periksa kembali isian Anda:</strong>
            <ul class="mb-0 ps-3">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" novalidate>
          @csrf

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="name"><i class="fa-solid fa-id-card"></i>Nama Lengkap</label>
              <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name') }}" autocomplete="name" required autofocus>
            </div>

            <div class="col-12 col-sm-6">
              <label class="form-label" for="username"><i class="fa-solid fa-user"></i>Username</label>
              <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror"
                     value="{{ old('username') }}" autocomplete="username" autocapitalize="none" spellcheck="false" required>
            </div>

            <div class="col-12 col-sm-6">
              <label class="form-label" for="email"><i class="fa-solid fa-envelope"></i>Email</label>
              <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}" autocomplete="email" inputmode="email" required>
            </div>

            <div class="col-12 col-sm-6">
              <label class="form-label" for="password"><i class="fa-solid fa-lock"></i>Password</label>
              <div class="input-group">
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       autocomplete="new-password" required>
                <button type="button" class="btn btn-outline-secondary" data-toggle-password="#password" aria-label="Tampilkan password">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="col-12 col-sm-6">
              <label class="form-label" for="password_confirmation"><i class="fa-solid fa-lock"></i>Ulangi Password</label>
              <div class="input-group">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                       autocomplete="new-password" required>
                <button type="button" class="btn btn-outline-secondary" data-toggle-password="#password_confirmation" aria-label="Tampilkan password">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
            </div>
          </div>

          <hr class="section-divider">

          <label class="form-label d-block"><i class="fa-solid fa-user-shield"></i>Peran (boleh lebih dari satu)</label>
          <div class="row g-2">
            <div class="col-12 col-sm-6">
              <label class="reg-role">
                <input class="form-check-input" type="checkbox" name="is_auditor" value="1" id="is_auditor" @checked(old('is_auditor'))>
                <span><strong>Auditor</strong><small>Membuat temuan dan memverifikasi perbaikan.</small></span>
              </label>
            </div>
            <div class="col-12 col-sm-6">
              <label class="reg-role">
                <input class="form-check-input" type="checkbox" name="is_auditee" value="1" id="is_auditee" @checked(old('is_auditee'))>
                <span><strong>Auditee</strong><small>Menindaklanjuti temuan untuk departemennya.</small></span>
              </label>
            </div>
          </div>
          <div class="reg-note"><i class="fa-solid fa-circle-info"></i><span>Peran Admin hanya dapat diberikan oleh administrator.</span></div>

          <div class="mt-3" id="dept-wrap" hidden>
            <label class="form-label d-block"><i class="fa-solid fa-building"></i>Departemen yang diampu</label>
            @php($selectedDepartments = collect(old('departments', []))->map(fn ($v) => (int) $v)->all())
            <div class="row g-2">
              @forelse ($departments as $d)
                <div class="col-12 col-sm-6">
                  <label class="reg-dept">
                    <input class="form-check-input" type="checkbox" name="departments[]" value="{{ $d->id }}" @checked(in_array($d->id, $selectedDepartments, true))>
                    <span>{{ $d->name }}</span>
                  </label>
                </div>
              @empty
                <div class="col-12"><div class="reg-note">Belum ada departemen. Hubungi admin untuk menambahkannya.</div></div>
              @endforelse
            </div>
          </div>

          <div class="k3-form-actions mt-4">
            <button type="submit" class="btn btn-primary px-4 flex-grow-1">
              <i class="fa-solid fa-user-plus"></i> Daftar
            </button>
          </div>
        </form>
      </div>
    </div>

    <p class="reg-foot">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
  </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // Departemen hanya relevan untuk Auditee
    var auditee = document.getElementById('is_auditee');
    var wrap = document.getElementById('dept-wrap');
    function syncDept() { wrap.hidden = !auditee.checked; }
    auditee.addEventListener('change', syncDept);
    syncDept();

    // Tampilkan / sembunyikan password
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.querySelector(btn.dataset.togglePassword);
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            btn.querySelector('i').className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        });
    });
})();
</script>
</body>
</html>
