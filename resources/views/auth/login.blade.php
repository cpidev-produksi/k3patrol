<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - K3 Patrol</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&family=Roboto:wght@400;500;700&family=Rubik:wght@400;500;600;700;800&family=Ubuntu+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#16211C;
    --ink-soft:#3B453F;
    --paper:#F3F4EC;
    --paper-deep:#E9EBDF;
    --panel:#FFFFFF;
    --green:#0E6B3B;
    --green-deep:#0A4C2A;
    --green-pale:#DCEEE1;
    --orange:#F2A20C;
    --orange-deep:#C57F00;
    --red:#D81E14;
    --red-deep:#A8140C;
    --line:#D3D7C7;
    --steel:#5B665F;
    --shadow: 0 10px 30px rgba(20,30,20,0.08);
    --shadow-lg: 0 20px 50px rgba(20,30,20,0.14);
    --radius: 10px;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{
    background:var(--paper);
    color:var(--ink);
    font-family:'Roboto', sans-serif;
    line-height:1.5;
    overflow-x:hidden;
  }
  img{max-width:100%;display:block;}
  a{color:inherit;text-decoration:none;}
  .wrap{max-width:1200px;margin:0 auto;padding:0 32px;}
  .mono{font-family:'Ubuntu Mono', monospace;}
  .display{font-family:'Arvo', sans-serif; font-weight:800; letter-spacing:0.01em;}
  .eyebrow{
    font-family:'Rubik', sans-serif;
    font-weight:600;
    letter-spacing:0.18em;
    text-transform:uppercase;
    font-size:13px;
    color:var(--green-deep);
    display:inline-flex;
    align-items:center;
    gap:10px;
  }
  .eyebrow::before{
    content:"";
    width:22px;height:3px;
    background:repeating-linear-gradient(45deg, var(--orange) 0 4px, var(--ink) 4px 8px);
    display:inline-block;
    border-radius:2px;
  }
  .grid-bg{
    background-image:
      linear-gradient(var(--line) 1px, transparent 1px),
      linear-gradient(90deg, var(--line) 1px, transparent 1px);
    background-size: 42px 42px;
    background-position: -1px -1px;
  }

  /* ===== NAV ===== */
  header{
    position:sticky; top:0; z-index:100;
    background:rgba(243,244,236,0.86);
    backdrop-filter:blur(10px);
    border-bottom:1px solid var(--line);
    transition:box-shadow .3s ease, padding .3s ease;
  }
  header.scrolled{ box-shadow:0 6px 24px rgba(20,30,20,0.08); }
  nav{
    display:flex; align-items:center; justify-content:space-between;
    padding:16px 0;
    transition:padding .3s ease;
  }
  header.scrolled nav{ padding:10px 0; }
  .brand{ display:flex; align-items:center; gap:12px; }
  .brand img{ width:40px; height:40px; transition:transform .5s ease; }
  .brand:hover img{ transform:rotate(45deg); }
  .brand-text{ display:flex; flex-direction:column; line-height:1.05; }
  .brand-text b{ font-family:'Arvo'; font-weight:800; font-size:20px; letter-spacing:0.02em; }
  .brand-text span{ font-size:10px; letter-spacing:0.14em; color:var(--steel); text-transform:uppercase; }
  .nav-links{ display:flex; gap:32px; align-items:center; }
  .nav-links a{
    font-family:'Rubik', sans-serif;
    font-size:14px; font-weight:500; color:var(--ink-soft);
    position:relative; padding:6px 0;
  }
  .nav-links a::after{
    content:""; position:absolute; left:0; bottom:0; height:2px; width:0;
    border-radius:2px;
    background:var(--orange); transition:width .25s ease;
  }
  .nav-links a:hover{ color:var(--ink); }
  .nav-links a:hover::after{ width:100%; }
  .btn{
    font-family:'Rubik'; font-weight:600; font-size:14px;
    padding:11px 22px; border-radius:6px; border:2px solid var(--ink);
    display:inline-flex; align-items:center; gap:8px;
    cursor:pointer; position:relative; overflow:hidden; z-index:1;
    transition:color .3s ease, border-color .3s ease, transform .15s ease;
  }
  .btn:active{ transform:scale(0.97); }
  .btn-primary{ background:var(--green); border-color:var(--green); color:#fff; }
  .btn-primary::before{
    content:""; position:absolute; inset:0; z-index:-1;
    background:repeating-linear-gradient(45deg, var(--orange) 0 14px, var(--orange-deep) 14px 28px);
    transform:translateX(-105%);
    transition:transform .38s cubic-bezier(.65,0,.35,1);
  }
  .btn-primary:hover{ border-color:var(--orange-deep); }
  .btn-primary:hover::before{ transform:translateX(0); }
  .btn-ghost{ background:transparent; color:var(--ink); }
  .btn-ghost:hover{ background:var(--ink); color:var(--paper); }

  /* ===== HERO ===== */
  .hero{ position:relative; padding:88px 0 60px; overflow:hidden; }
  .hero-grid{ display:grid; grid-template-columns:1.05fr 0.95fr; gap:48px; align-items:center; }
  .hero h1{
    font-size:clamp(40px, 5.4vw, 68px);
    line-height:1.02;
    margin:18px 0 22px;
  }
  .hero h1 em{ font-style:normal; color:var(--green); }
  .hero p.lead{ font-size:17px; color:var(--ink-soft); max-width:480px; margin-bottom:30px; }
  .hero-actions{ display:flex; gap:14px; margin-bottom:40px; flex-wrap:wrap; }
  .stat-chips{ display:flex; gap:10px; flex-wrap:wrap; }
  .chip{
    background:var(--panel); border:1px solid var(--line); border-radius:8px;
    padding:10px 14px; box-shadow:var(--shadow);
    display:flex; flex-direction:column; min-width:130px;
  }
  .chip b{ font-family:'Arvo'; font-size:26px; font-weight:800; }
  .chip span{ font-size:11px; color:var(--steel); text-transform:uppercase; letter-spacing:0.06em; }
  .chip.red b{ color:var(--red); }
  .chip.orange b{ color:var(--orange-deep); }
  .chip.green b{ color:var(--green); }

  .hero-visual{ position:relative; display:flex; align-items:center; justify-content:center; height:520px; }
  .gear-ring{
    position:absolute; width:340px; height:340px;
    animation:spin 60s linear infinite;
    opacity:0.9;
  }
  @keyframes spin{ from{transform:rotate(0deg);} to{transform:rotate(360deg);} }
  .orbit-path{
    position:absolute; width:460px; height:460px;
    border:2px dashed var(--steel); border-radius:50%; opacity:0.35;
  }
  .dock{
    position:absolute; background:var(--panel); border:1px solid var(--line);
    border-radius:8px; padding:10px 12px; box-shadow:var(--shadow);
    width:150px; font-size:11.5px; line-height:1.3;
    transition:transform .25s ease, box-shadow .25s ease;
  }
  .dock:hover{ transform:translateY(-4px) scale(1.03); box-shadow:var(--shadow-lg); z-index:5; }
  .dock b{ display:block; font-family:'Arvo'; font-size:14px; letter-spacing:0.03em; }
  .dock .dot{ width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
  .dock-1{ top:0; left:50%; transform:translate(-50%,-10%); }
  .dock-2{ top:50%; right:0; transform:translate(20%,-50%); }
  .dock-3{ bottom:0; left:50%; transform:translate(-50%,10%); }
  .dock-4{ top:50%; left:0; transform:translate(-20%,-50%); }
  .dock-1 .dot{ background:var(--red); }
  .dock-2 .dot{ background:var(--orange); }
  .dock-3 .dot{ background:var(--green); }
  .dock-4 .dot{ background:var(--green-deep); }

  section{ padding:90px 0; }
  .section-head{ max-width:620px; margin-bottom:56px; }
  .section-head h2{ font-size:clamp(30px,3.6vw,44px); margin-top:14px; }
  .section-head p{ color:var(--ink-soft); margin-top:14px; font-size:15.5px; }
  .reveal{ opacity:0; transform:translateY(24px); transition:opacity .7s ease, transform .7s ease; }
  .reveal.in{ opacity:1; transform:translateY(0); }

  .flow-steps{ display:flex; flex-direction:column; gap:30px; }
  .flow-step{
    display:grid; grid-template-columns:76px 1fr; gap:24px; position:relative;
    background:var(--panel); border:1px solid var(--line); border-radius:var(--radius);
    padding:26px 28px; box-shadow:var(--shadow); transition:transform .25s ease, box-shadow .25s ease;
  }
  .flow-step:hover{ transform:translateX(6px); box-shadow:var(--shadow-lg); }
  .flow-num{
    font-family:'Arvo'; font-weight:900; font-size:40px;
    color:var(--panel); background:var(--ink);
    width:56px; height:56px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    align-self:start; border:3px solid var(--paper); box-shadow:0 0 0 2px var(--ink);
  }
  .flow-step:nth-child(1) .flow-num{ background:var(--red); }
  .flow-step:nth-child(2) .flow-num{ background:var(--orange-deep); }
  .flow-step:nth-child(3) .flow-num{ background:var(--green); }
  .flow-step:nth-child(4) .flow-num{ background:var(--green-deep); }
  .flow-step h3{ font-family:'Arvo'; font-size:22px; font-weight:800; letter-spacing:0.02em; margin-bottom:6px;}
  .flow-step p{ color:var(--ink-soft); font-size:14.5px; max-width:560px; }
  .flow-tag{
    font-family:'Ubuntu Mono'; font-size:11px; color:var(--steel);
    display:inline-block; margin-top:10px; padding:3px 8px; border:1px solid var(--line); border-radius:4px;
  }

  .roles-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
  .role-card{
    background:var(--panel); border:1px solid var(--line); border-radius:var(--radius);
    padding:30px 26px; position:relative; overflow:hidden;
    box-shadow:var(--shadow); transition:transform .3s ease, box-shadow .3s ease;
  }
  .role-card::before{ content:""; position:absolute; top:0; left:0; right:0; height:6px; }
  .role-card:nth-child(1)::before{ background:var(--red); }
  .role-card:nth-child(2)::before{ background:var(--orange); }
  .role-card:nth-child(3)::before{ background:var(--green); }
  .role-card:hover{ transform:translateY(-8px); box-shadow:var(--shadow-lg); }
  .role-icon{
    width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    font-family:'Arvo'; font-weight:800; font-size:20px; color:#fff; margin-bottom:18px;
  }
  .role-card:nth-child(1) .role-icon{ background:var(--red); }
  .role-card:nth-child(2) .role-icon{ background:var(--orange-deep); }
  .role-card:nth-child(3) .role-icon{ background:var(--green); }
  .role-card h3{ font-family:'Arvo'; font-size:23px; font-weight:800; margin-bottom:10px; }
  .role-card p{ color:var(--ink-soft); font-size:14.5px; margin-bottom:16px; }
  .role-card ul{ list-style:none; font-size:13px; color:var(--ink-soft); }
  .role-card li{ padding-left:18px; position:relative; margin-bottom:6px; }
  .role-card li::before{ content:"—"; position:absolute; left:0; color:var(--steel); }

  .feat-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
  .feat-card{
    background:var(--panel); border:1px solid var(--line); border-radius:var(--radius);
    padding:26px; transition:transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    position:relative;
  }
  .feat-card:hover{ transform:translateY(-6px); border-color:var(--green); box-shadow:var(--shadow-lg); }
  .feat-num{ font-family:'Ubuntu Mono'; font-size:12px; color:var(--steel); }
  .feat-card h4{ font-family:'Arvo'; font-size:19px; font-weight:800; margin:10px 0 8px; }
  .feat-card p{ font-size:13.5px; color:var(--ink-soft); }

  .band{ background:var(--ink); color:var(--paper); padding:80px 0; position:relative; overflow:hidden; }
  .band-texture{
    position:absolute; right:-60px; top:50%; transform:translateY(-50%);
    width:420px; opacity:0.14; filter:grayscale(1) invert(1);
    pointer-events:none;
  }
  .band-inner{ display:grid; grid-template-columns:1.2fr 0.8fr; gap:40px; align-items:center; position:relative; z-index:2; }
  .band h2{ font-size:clamp(28px,3.4vw,38px); margin-bottom:16px; }
  .band p{ color:#C7CFC5; max-width:460px; font-size:15px; margin-bottom:26px; }
  .band-stats{ display:grid; grid-template-columns:1fr 1fr; gap:18px; }
  .band-stat b{ font-family:'Arvo'; font-size:38px; font-weight:800; display:block; color:var(--orange); }
  .band-stat span{ font-size:12px; color:#9FAA98; text-transform:uppercase; letter-spacing:0.06em; }

  /* ===== LOGIN SECTION (replaces final CTA) ===== */
  .login-section{ text-align:center; }
  .login-section .section-head{ margin-left:auto; margin-right:auto; text-align:center; }
  .login-card{
    max-width:420px; margin:0 auto; background:var(--panel); border:1px solid var(--line);
    border-radius:var(--radius); box-shadow:var(--shadow-lg); padding:34px 32px; text-align:left;
    position:relative; overflow:hidden;
  }
  .login-card::before{
    content:""; position:absolute; top:0; left:0; right:0; height:5px;
    background:repeating-linear-gradient(45deg, var(--orange) 0 14px, var(--ink) 14px 28px);
  }
  .field-group{ margin-bottom:18px; }
  .field-group label{
    font-family:'Rubik', sans-serif;
    display:block; font-size:11.5px; font-weight:600; text-transform:uppercase;
    letter-spacing:0.08em; color:var(--steel); margin-bottom:6px;
  }
  .field-group input{
    width:100%; padding:11px 14px; border:1.5px solid var(--line); border-radius:7px;
    font-family:'Roboto'; font-size:14.5px; background:var(--paper); color:var(--ink);
    transition:border-color .2s ease, box-shadow .2s ease;
  }
  .field-group input:focus{ outline:none; border-color:var(--green); box-shadow:0 0 0 3px var(--green-pale); background:var(--panel); }
  .login-submit{ width:100%; justify-content:center; margin-top:6px; }
  .form-alert{
    border-left:4px solid; border-radius:6px; padding:10px 14px; font-size:13.5px; margin-bottom:16px;
  }
  .form-alert.error{ background:#FDECEB; color:var(--red-deep); border-left-color:var(--red); }
  .form-alert.success{ background:var(--green-pale); color:var(--green-deep); border-left-color:var(--green); }

  footer{ border-top:1px solid var(--line); padding:44px 0 30px; }
  .foot-grid{ display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:24px; margin-bottom:30px;}
  .foot-links{ display:flex; gap:40px; flex-wrap:wrap; }
  .foot-col b{ font-family:'Rubik'; font-weight:600; font-size:13px; letter-spacing:0.1em; text-transform:uppercase; display:block; margin-bottom:12px; color:var(--steel); }
  .foot-col a{ display:block; font-size:13.5px; color:var(--ink-soft); margin-bottom:8px; }
  .foot-col a:hover{ color:var(--green); }
  .foot-bottom{ display:flex; justify-content:space-between; align-items:center; font-size:12.5px; color:var(--steel); border-top:1px solid var(--line); padding-top:20px; flex-wrap:wrap; gap:10px; }

  @media (max-width:960px){
    .hero-grid{ grid-template-columns:1fr; }
    .hero-visual{ height:400px; }
    .roles-grid{ grid-template-columns:1fr; }
    .feat-grid{ grid-template-columns:1fr 1fr; }
    .band-inner{ grid-template-columns:1fr; }
  }
  @media (max-width:640px){
    .nav-links{ display:none; }
    .feat-grid{ grid-template-columns:1fr; }
    .flow-step{ grid-template-columns:48px 1fr; padding:20px; }
    .flow-num{ width:44px; height:44px; font-size:28px; }
    .gear-ring{ width:220px; height:220px; }
    .orbit-path{ width:300px; height:300px; }
    .dock{ width:120px; font-size:10.5px; padding:8px; }
    .login-card{ padding:26px 20px; }
  }
  /* ===== RESPONSIVE TAMBAHAN ===== */
  .alt-link{ text-align:center; margin-top:18px; font-size:14px; color:var(--ink-soft); }
  .alt-link a{ color:var(--green); font-weight:600; text-decoration:underline; text-underline-offset:3px; }
  .alt-link a:hover{ color:var(--green-deep); }
  section[id]{ scroll-margin-top:72px; }
  img, svg{ max-width:100%; }

  @media (max-width:960px){
    .wrap{ padding:0 24px; }
    section{ padding:72px 0; }
    .nav-links{ gap:20px; }
    .hero{ padding:64px 0 48px; }
    .band{ padding:64px 0; }
    .section-head{ margin-bottom:40px; }
  }
  @media (max-width:640px){
    .wrap{ padding:0 18px; }
    section{ padding:52px 0; }
    nav{ padding:12px 0; }
    .brand img{ width:34px; height:34px; }
    .brand-text b{ font-size:18px; }
    .hero{ padding:40px 0 36px; }
    .hero h1{ font-size:clamp(30px, 9vw, 38px); margin:14px 0 16px; }
    .hero p.lead{ font-size:15.5px; margin-bottom:22px; }
    .hero-actions{ margin-bottom:28px; }
    .hero-actions .btn{ flex:1 1 100%; justify-content:center; }
    .stat-chips{ display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .chip{ min-width:0; padding:10px 12px; }
    .chip b{ font-size:22px; }
    .hero-visual{ height:340px; }
    .dock-2{ transform:translate(8%,-50%); }
    .dock-4{ transform:translate(-8%,-50%); }
    .section-head{ margin-bottom:28px; }
    .section-head h2{ font-size:clamp(24px, 7vw, 30px); }
    .flow-steps{ gap:16px; }
    .flow-step{ gap:14px; padding:18px 16px; }
    .flow-step:hover{ transform:none; }
    .flow-step h3{ font-size:19px; }
    .role-card{ padding:22px 18px; }
    .feat-card{ padding:20px 18px; }
    .band{ padding:48px 0; }
    .band-stats{ gap:14px; }
    .band-stat b{ font-size:30px; }
    .band-texture{ width:260px; right:-90px; }
    .login-card{ padding:26px 18px 22px; }
    .field-group input{ font-size:16px; }
    .foot-grid{ flex-direction:column; }
    .foot-links{ gap:28px; }
    .foot-bottom{ flex-direction:column; align-items:flex-start; }
  }
  @media (prefers-reduced-motion: reduce){
    *{ animation-duration:0.001ms !important; transition-duration:0.001ms !important; }
  }
</style>
</head>
<body>

<header id="siteHeader">
  <div class="wrap">
    <nav>
      <a href="#top" class="brand">
        <img src="{{ asset('images/icon_k3.png') }}" alt="K3 Patrol">
        <span class="brand-text"><b>K3 PATROL</b><span>PT. Charoen Pokphand Indonesia • Food Division</span></span>
      </a>
      <div class="nav-links">
        <a href="#alur">Alur Kerja</a>
        <a href="#peran">Peran Tim</a>
        <a href="#fitur">Fitur</a>
        <a href="#footer-contact">Kontak</a>
      </div>
      <a href="#login" class="btn btn-primary">Login</a>
    </nav>
  </div>
</header>

<main id="top">
  <!-- HERO -->
  <section class="hero grid-bg">
    <div class="wrap hero-grid">
      <div>
        <span class="eyebrow">Sistem Monitoring K3 Patrol</span>
        <h1 class="display">Setiap temuan terpantau.<br><em>Setiap bahaya</em> terverifikasi.</h1>
        <p class="lead">K3 Patrol menghubungkan auditor lapangan, safety officer, dan manajemen dalam satu alur kerja — dari laporan di mobile hingga verifikasi perbaikan, real-time dan terekam rapi.</p>
        <div class="hero-actions">
          <a href="#login" class="btn btn-primary">Masuk ke Sistem</a>
          <a href="#fitur" class="btn btn-ghost">Jelajahi Fitur</a>
        </div>
        <div class="stat-chips">
          <div class="chip red"><b class="counter" data-target="128">0</b><span>Temuan bulan ini</span></div>
          <div class="chip orange"><b class="counter" data-target="94" data-suffix="%">0</b><span>Diverifikasi &lt;48 jam</span></div>
          <div class="chip green"><b class="counter" data-target="36">0</b><span>Auditor aktif</span></div>
        </div>
      </div>
      <div class="hero-visual">
        <div class="orbit-path"></div>
        <img src="{{ asset('images/icon_k3.png') }}" class="gear-ring" alt="">
        <div class="dock dock-1"><b><span class="dot"></span>Temuan Baru</b>Kebocoran pipa area B — Risiko Tinggi <span class="mono" style="opacity:.6">#TM-2291</span></div>
        <div class="dock dock-2"><b><span class="dot"></span>Alert Terkirim</b>Notifikasi ke Safety Officer &amp; Manajer Area</div>
        <div class="dock dock-3"><b><span class="dot"></span>Terverifikasi</b>Perbaikan dikonfirmasi auditor di lokasi</div>
        <div class="dock dock-4"><b><span class="dot"></span>Perbaikan</b>Auditee unggah bukti foto tindak lanjut</div>
      </div>
    </div>
  </section>

  <!-- WORKFLOW -->
  <section id="alur">
    <div class="wrap">
      <div class="section-head reveal">
        <span class="eyebrow">Alur Kerja</span>
        <h2 class="display">Dari temuan di lapangan sampai tuntas terverifikasi</h2>
        <p>Empat tahap yang mengunci setiap temuan sampai benar-benar selesai — tidak ada laporan yang menggantung.</p>
      </div>
      <div class="flow reveal">
        <div class="flow-steps">
          <div class="flow-step">
            <div class="flow-num">01</div>
            <div>
              <h3>Lapor di Lapangan</h3>
              <p>Auditor menemukan potensi bahaya saat patroli rutin — difoto, dikategorikan tingkat risikonya, dan dicatat langsung dari aplikasi mobile lengkap dengan lokasi.</p>
              <span class="flow-tag">app: mobile · auditor</span>
            </div>
          </div>
          <div class="flow-step">
            <div class="flow-num">02</div>
            <div>
              <h3>Notifikasi Terkirim</h3>
              <p>Sistem mengirim alert real-time ke Safety Officer terkait dan seluruh user relevan, dengan tingkat urgensi yang jelas agar tidak ada yang terlewat.</p>
              <span class="flow-tag">trigger: auto-notify</span>
            </div>
          </div>
          <div class="flow-step">
            <div class="flow-num">03</div>
            <div>
              <h3>Perbaikan Dilakukan</h3>
              <p>Safety Officer selaku Auditee menindaklanjuti temuan, mengunggah bukti perbaikan berupa foto dan catatan langsung dari lokasi kejadian.</p>
              <span class="flow-tag">role: safety officer / auditee</span>
            </div>
          </div>
          <div class="flow-step">
            <div class="flow-num">04</div>
            <div>
              <h3>Verifikasi Tuntas</h3>
              <p>Auditor meninjau kembali hasil perbaikan, memverifikasi langsung di lapangan, lalu menutup temuan dengan status selesai dan terekam di riwayat audit.</p>
              <span class="flow-tag">status: closed &amp; logged</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ROLES -->
  <section id="peran" style="background:var(--paper-deep);">
    <div class="wrap">
      <div class="section-head reveal">
        <span class="eyebrow">Peran Tim</span>
        <h2 class="display">Satu sistem, tiga peran yang saling terhubung</h2>
        <p>Setiap peran punya tugas jelas, dan setiap tindakan langsung terlihat oleh peran lainnya.</p>
      </div>
      <div class="roles-grid reveal">
        <div class="role-card">
          <div class="role-icon">A</div>
          <h3>Auditor</h3>
          <p>Menyisir area kerja dan mencatat temuan potensi bahaya langsung dari genggaman.</p>
          <ul>
            <li>Foto &amp; kategori temuan di lokasi</li>
            <li>Penentuan tingkat risiko</li>
            <li>Verifikasi akhir hasil perbaikan</li>
          </ul>
        </div>
        <div class="role-card">
          <div class="role-icon">S</div>
          <h3>Safety Officer</h3>
          <p>Menerima notifikasi instan dan menindaklanjuti perbaikan sebagai Auditee.</p>
          <ul>
            <li>Alert &amp; warning real-time</li>
            <li>Unggah bukti perbaikan</li>
            <li>Update status penyelesaian</li>
          </ul>
        </div>
        <div class="role-card">
          <div class="role-icon">M</div>
          <h3>Admin / Manajemen</h3>
          <p>Memantau seluruh temuan dan performa tindak lanjut dari satu dashboard menyeluruh.</p>
          <ul>
            <li>Tren area &amp; departemen rawan</li>
            <li>Riwayat &amp; jejak audit lengkap</li>
            <li>Kelola pengguna dan kategori</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section id="fitur">
    <div class="wrap">
      <div class="section-head reveal">
        <span class="eyebrow">Fitur Unggulan</span>
        <h2 class="display">Semua yang dibutuhkan tim K3, dalam satu platform</h2>
      </div>
      <div class="feat-grid reveal">
        <div class="feat-card"><span class="feat-num">/01</span><h4>Notifikasi &amp; Alert Real-Time</h4><p>Warning otomatis ke user terkait begitu temuan baru dilaporkan, tanpa jeda.</p></div>
        <div class="feat-card"><span class="feat-num">/02</span><h4>Verifikasi Foto Sebelum/Sesudah</h4><p>Bukti visual temuan dan perbaikan tersimpan berdampingan untuk validasi cepat.</p></div>
        <div class="feat-card"><span class="feat-num">/03</span><h4>Klasifikasi Tingkat Risiko</h4><p>Setiap temuan diberi level urgensi agar prioritas penanganan jelas sejak awal.</p></div>
        <div class="feat-card"><span class="feat-num">/04</span><h4>Riwayat &amp; Jejak Audit</h4><p>Seluruh proses dari lapor hingga verifikasi tercatat rapi dan dapat ditelusuri.</p></div>
        <div class="feat-card"><span class="feat-num">/05</span><h4>Dashboard Analitik</h4><p>Pantau tren temuan per departemen dan kecepatan tindak lanjut secara visual.</p></div>
        <div class="feat-card"><span class="feat-num">/06</span><h4>Akses Multi-Peran</h4><p>Auditor, Auditee, dan Admin bekerja dalam satu sistem dengan hak akses masing-masing.</p></div>
      </div>
    </div>
  </section>

  <!-- TRUST BAND -->
  <section class="band">
    <img src="{{ asset('images/logo_CP_line.png') }}" class="band-texture" alt="">
    <div class="wrap band-inner">
      <div>
        <span class="eyebrow" style="color:var(--orange);">Selalu Siaga</span>
        <h2 class="display">Waspada seperti sang singa penjaga — tidak ada temuan yang lolos dari pantauan</h2>
        <p>Setiap laporan dikawal sampai tuntas: dari mobile auditor, ke meja safety officer, kembali diverifikasi di lapangan.</p>
      </div>
      <div class="band-stats">
        <div class="band-stat"><b class="counter" data-target="1240">0</b><span>Total Temuan Tercatat</span></div>
        <div class="band-stat"><b class="counter" data-target="98" data-suffix="%">0</b><span>Tingkat Penyelesaian</span></div>
        <div class="band-stat"><b class="counter" data-target="18">0</b><span>Departemen Terhubung</span></div>
        <div class="band-stat"><b class="counter" data-target="2.4" data-decimal="1" data-suffix=" jam">0</b><span>Rata-rata Respon</span></div>
      </div>
    </div>
  </section>

  <!-- LOGIN SECTION (was final CTA) -->
  <section class="login-section grid-bg" id="login">
    <div class="wrap">
      <div class="section-head reveal">
        <span class="eyebrow">Masuk ke Sistem</span>
        <h2 class="display">Masuk ke Dashboard</h2>
        <p>Gunakan akun yang terdaftar untuk mulai memantau, melapor, atau menindaklanjuti temuan.</p>
      </div>

      <div class="login-card reveal">
        @if ($errors->any())
          <div class="form-alert error">{{ $errors->first() }}</div>
        @endif
        @if (session('message'))
          <div class="form-alert success">{{ session('message') }}</div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
          @csrf
          <div class="field-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
          </div>
          <div class="field-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary login-submit">Login →</button>
        </form>
        <p class="alt-link">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
      </div>
    </div>
  </section>
</main>

<footer id="footer-contact">
  <div class="wrap">
    <div class="foot-grid">
      <a href="#top" class="brand">
        <img src="{{ asset('images/icon_k3.png') }}" alt="K3 Patrol" style="width:36px;height:36px;">
        <span class="brand-text"><b>K3 PATROL</b><span>Kesehatan &amp; Keselamatan Kerja</span><span>PT. Charoen Pokphand Indonesia • Food Division Salatiga</span></span>
      </a>
      <div class="foot-links">
        <div class="foot-col">
          <b>Navigasi</b>
          <a href="#alur">Alur Kerja</a>
          <a href="#peran">Peran Tim</a>
          <a href="#fitur">Fitur</a>
        </div>
        <div class="foot-col">
          <b>Kontak</b>
          <a href="mailto:k3patrol@perusahaan.com">k3patrol@perusahaan.com</a>
          <a href="tel:+622100000000">+62 21 0000 0000</a>
        </div>
        <div class="foot-col">
          <b>Sistem</b>
          <a href="#">Panduan Auditor</a>
          <a href="#">Panduan Safety Officer</a>
        </div>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© 2026 K3 Patrol. Kesehatan &amp; Keselamatan Kerja.</span>
      <span class="mono">v1.0 · patrol-system</span>
    </div>
  </div>
</footer>

<script>
  const header = document.getElementById('siteHeader');
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 10);
  });

  const revealEls = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); } });
  }, {threshold:0.15});
  revealEls.forEach(el=>io.observe(el));

  const counters = document.querySelectorAll('.counter');
  const animateCounter = (el) => {
    const target = parseFloat(el.dataset.target);
    const decimals = parseInt(el.dataset.decimal || "0");
    const suffix = el.dataset.suffix || "";
    const duration = 1400;
    const start = performance.now();
    function tick(now){
      const p = Math.min((now-start)/duration, 1);
      const eased = 1 - Math.pow(1-p, 3);
      const val = target*eased;
      el.textContent = val.toFixed(decimals) + suffix;
      if(p<1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  };
  const cio = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ animateCounter(e.target); cio.unobserve(e.target); } });
  }, {threshold:0.5});
  counters.forEach(c=>cio.observe(c));

  // prevent back-navigation to protected pages after logout (kept from original login page)
  (function() {
    'use strict';
    history.pushState(null, null, location.href);
    window.onpopstate = function() {
      history.pushState(null, null, location.href);
    };
    try {
      localStorage.clear();
      sessionStorage.clear();
    } catch (e) {}
  })();
</script>
</body>
</html>
