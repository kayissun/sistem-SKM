<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIPUAS — Survei Kepuasan Masyarakat</title>
  <meta name="description" content="Sistem digital Survei Kepuasan Masyarakat untuk Dinas Kesehatan dan jaringan Puskesmas/RSU — kuesioner fleksibel, isi via QR code, laporan IKM real-time.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,wght@0,600;1,500;1,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root {
      --purple-900: #180733;
      --purple-800: #2E1065;
      --purple-700: #6D28D9;
      --purple-600: #7C3AED;
      --purple-500: #8b5cf6;
      --purple-100: #EDE9FE;
      --purple-50: #FAF8FF;
      --ink: #14102B;
      --ink-muted: #625B78;
      --gold-700: #A66A0E;
      --gold-600: #C88719;
      --gold-100: #FCF1DC;
      --white: #FFFFFF;

      --gradient-primary: linear-gradient(135deg, #7C3AED 0%, #5B21B6 55%, #2A0B5E 100%);
      --gradient-hero-bg: radial-gradient(120% 100% at 50% 0%, #EEE9FF 0%, #FAF8FF 55%, #F5F1FF 100%);
      --gradient-card: linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(255, 255, 255, 0.72) 100%);
      --gradient-gold: linear-gradient(135deg, #E4A63B 0%, #C88719 100%);

      /* Kawung-inspired tile: four petals radiating from a center point, echoing the
         batik motifs of Central Java without reproducing any specific artwork. */
      --pattern-purple: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='72'%20height='72'%3E%3Cg%20fill='none'%20stroke='%236D28D9'%20stroke-opacity='0.10'%20stroke-width='1.1'%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(45%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(90%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(135%2036%2036)'/%3E%3Ccircle%20cx='36'%20cy='36'%20r='3.5'/%3E%3C/g%3E%3C/svg%3E");
      --pattern-gold: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='72'%20height='72'%3E%3Cg%20fill='none'%20stroke='%23E4A63B'%20stroke-opacity='0.16'%20stroke-width='1.1'%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(45%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(90%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(135%2036%2036)'/%3E%3Ccircle%20cx='36'%20cy='36'%20r='3.5'/%3E%3C/g%3E%3C/svg%3E");

      --radius-lg: 24px;
      --radius-md: 16px;
      --shadow-sm: 0 4px 6px -1px rgba(24, 7, 51, 0.05);
      --shadow-md: 0 12px 24px -8px rgba(46, 16, 101, 0.18);
      --shadow-lg: 0 24px 40px -10px rgba(46, 16, 101, 0.22), 0 8px 12px -6px rgba(46, 16, 101, 0.08);
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    body {
      margin: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--ink);
      background: var(--white);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    a { text-decoration: none; color: inherit; }
    img, svg { display: block; max-width: 100%; }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 24px;
    }

    em.stamp {
      font-family: 'Fraunces', serif;
      font-style: italic;
      font-weight: 600;
    }

    /* ---------- Top strip ---------- */
    .top-bar {
      height: 4px;
      background: var(--gradient-gold);
    }

    /* ---------- Navbar ---------- */
    .navbar {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(250, 248, 255, 0.9);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(46, 16, 101, 0.08);
      transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .navbar.is-scrolled {
      box-shadow: 0 8px 24px -12px rgba(24, 7, 51, 0.18);
      border-bottom-color: rgba(46, 16, 101, 0.14);
    }

    .navbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 14px 24px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand img {
      width: 40px;
      height: auto;
      border-radius: 10px;
    }

    .brand-text {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }

    .brand-text strong {
      font-weight: 800;
      font-size: 1.05rem;
      color: var(--purple-900);
      letter-spacing: 0.01em;
    }

    .brand-text span {
      font-size: 0.72rem;
      color: var(--ink-muted);
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 4px;
      font-weight: 600;
      font-size: 0.92rem;
      color: var(--ink-muted);
    }

    .nav-links a {
      padding: 8px 14px;
      border-radius: 99px;
      transition: color 0.2s, background 0.2s;
    }

    .nav-links a:hover {
      color: var(--purple-700);
      background: var(--purple-100);
    }

    /* ---------- Buttons ---------- */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-weight: 700;
      font-size: 0.94rem;
      border-radius: 99px;
      padding: 13px 26px;
      transition: all 0.25s ease;
      cursor: pointer;
      border: 2px solid transparent;
      white-space: nowrap;
    }

    .btn i { font-size: 0.85em; }

    .btn-primary {
      background: var(--gradient-primary);
      color: var(--white);
      box-shadow: 0 10px 20px -6px rgba(46, 16, 101, 0.45);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 16px 26px -6px rgba(46, 16, 101, 0.55);
    }

    .btn-outline {
      border-color: rgba(109, 40, 217, 0.22);
      color: var(--purple-700);
      background: var(--white);
      box-shadow: var(--shadow-sm);
    }

    .btn-outline:hover {
      border-color: var(--purple-700);
      background: var(--purple-100);
      transform: translateY(-2px);
    }

    .btn-sm { padding: 9px 20px; font-size: 0.86rem; }

    /* ---------- Hero ---------- */
    .hero {
      position: relative;
      padding: 28px 0;
      background: var(--gradient-hero-bg);
      border-bottom: 1px solid rgba(109, 40, 217, 0.06);
      min-height: calc(100svh - 61px);
      display: flex;
      align-items: center;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: var(--pattern-purple);
      background-size: 72px 72px;
      opacity: 0.7;
      pointer-events: none;
      mask-image: radial-gradient(70% 70% at 78% 30%, #000 0%, transparent 75%);
    }

    .hero .container { width: 100%; position: relative; z-index: 1; }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.05fr 0.95fr;
      gap: 40px;
      align-items: center;
    }

    .hero-text { animation: rise-in 0.7s cubic-bezier(.22,.9,.32,1) both; }
    .hero-illustration { animation: rise-in 0.8s 0.15s cubic-bezier(.22,.9,.32,1) both; }

    @keyframes rise-in {
      from { opacity: 0; transform: translateY(14px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--white);
      color: var(--purple-700);
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      padding: 7px 16px 7px 12px;
      border-radius: 99px;
      margin-bottom: 18px;
      border: 1px solid rgba(109, 40, 217, 0.15);
      box-shadow: var(--shadow-sm);
    }

    .eyebrow i {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--gradient-gold);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.62rem;
    }

    .hero h1 {
      font-size: clamp(1.9rem, 3.1vw, 2.85rem);
      font-weight: 800;
      line-height: 1.18;
      letter-spacing: -0.02em;
      margin: 0 0 16px;
      color: var(--purple-900);
    }

    .hero h1 .accent {
      font-family: 'Fraunces', serif;
      font-style: italic;
      font-weight: 600;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero p.lead {
      font-size: 1.02rem;
      color: var(--ink-muted);
      max-width: 500px;
      margin: 0 0 26px;
      font-weight: 400;
    }

    .hero-cta {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 22px;
    }

    .hero-note {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.85rem;
      color: var(--ink-muted);
      font-weight: 500;
    }

    .hero-note i { color: var(--purple-700); font-size: 0.95rem; }

    .hero-illustration {
      position: relative;
      max-width: 420px;
      margin: 0 auto;
    }

    .hero-illustration svg,
    .hero-illustration img {
      width: 100%;
      height: auto;
      max-height: 60svh;
      display: block;
      filter: drop-shadow(0 20px 30px rgba(46, 16, 101, 0.16));
    }

    .hero-illustration {
      width: 100%;
      max-width: 420px;
      margin: 0 auto;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .hero-illustration img {
      width: 100% !important;
      height: auto !important;
      max-height: none !important;
      display: block;
    }

    /* ---------- Stat strip ---------- */
    .stat-strip {
      padding: 30px 0;
      border-bottom: 1px solid rgba(109, 40, 217, 0.08);
    }

    .stat-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }

    .stat-item {
      display: flex;
      align-items: center;
      gap: 14px;
      justify-content: center;
      text-align: left;
    }

    .stat-item i {
      width: 40px;
      height: 40px;
      flex-shrink: 0;
      border-radius: 12px;
      background: var(--purple-100);
      color: var(--purple-700);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
    }

    .stat-item strong {
      display: block;
      font-size: 0.92rem;
      font-weight: 700;
      color: var(--purple-900);
    }

    .stat-item small {
      display: block;
      font-size: 0.78rem;
      color: var(--ink-muted);
      font-weight: 500;
    }

    /* ---------- Section shared ---------- */
    section { padding: 88px 0; }

    .section-head {
      max-width: 620px;
      margin: 0 auto 52px;
      text-align: center;
    }

    .section-head .eyebrow { margin-bottom: 16px; }

    .section-head h2 {
      font-size: clamp(1.8rem, 2.8vw, 2.25rem);
      font-weight: 800;
      margin: 0 0 14px;
      color: var(--purple-900);
      letter-spacing: -0.01em;
    }

    .section-head p {
      color: var(--ink-muted);
      margin: 0;
      font-size: 1.03rem;
    }

    /* ---------- Features ---------- */
    .features-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 22px;
    }

    .feature-card {
      background: var(--gradient-card);
      backdrop-filter: blur(8px);
      border-radius: var(--radius-md);
      padding: 30px 24px;
      border: 1px solid rgba(255, 255, 255, 0.9);
      box-shadow: var(--shadow-sm);
      transition: all 0.25s ease;
    }

    .feature-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-md);
      border-color: rgba(109, 40, 217, 0.2);
    }

    .feature-icon {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      background: var(--gradient-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 8px 16px -4px rgba(46, 16, 101, 0.35);
      color: var(--white);
      font-size: 1.15rem;
    }

    .feature-card h3 {
      font-size: 1.08rem;
      font-weight: 700;
      margin: 0 0 10px;
      color: var(--purple-900);
    }

    .feature-card p {
      font-size: 0.92rem;
      color: var(--ink-muted);
      margin: 0;
      line-height: 1.6;
    }

    /* ---------- How it works ---------- */
    .how-wrap {
      background: var(--white);
      border-radius: 32px;
      margin: 0 0 40px;
      padding: 56px 32px 48px;
      box-shadow: var(--shadow-sm);
      border: 1px solid rgba(109, 40, 217, 0.06);
    }

    .how-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;
      position: relative;
    }

    .how-grid::before {
      content: '';
      position: absolute;
      top: 30px;
      left: calc(100% / 6);
      right: calc(100% / 6);
      height: 0;
      border-top: 2px dashed rgba(109, 40, 217, 0.25);
      z-index: 0;
    }

    .how-step { text-align: center; padding: 16px; position: relative; z-index: 1; }

    .how-step .num {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--gradient-primary);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      margin: 0 auto 22px;
      box-shadow: 0 10px 20px -5px rgba(46, 16, 101, 0.4);
      border: 4px solid var(--white);
    }

    .how-step h3 {
      font-size: 1.08rem;
      font-weight: 700;
      margin: 0 0 8px;
      color: var(--purple-900);
    }

    .how-step p {
      font-size: 0.92rem;
      color: var(--ink-muted);
      margin: 0;
    }

    /* ---------- CTA Banner ---------- */
    .cta-banner {
      position: relative;
      background: var(--gradient-primary);
      border-radius: var(--radius-lg);
      padding: 60px 52px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 32px;
      flex-wrap: wrap;
      color: var(--white);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }

    .cta-banner::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: var(--pattern-gold);
      background-size: 72px 72px;
      mask-image: radial-gradient(60% 100% at 100% 0%, #000 0%, transparent 70%);
    }

    .cta-banner > * { position: relative; z-index: 1; }

    .cta-banner h2 {
      font-size: 1.75rem;
      font-weight: 800;
      margin: 0 0 12px;
    }

    .cta-banner p {
      color: rgba(255, 255, 255, 0.82);
      margin: 0;
      max-width: 460px;
      font-size: 1.02rem;
    }

    .cta-banner .btn-primary {
      background: var(--white);
      color: var(--purple-700);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .cta-banner .btn-primary:hover {
      background: var(--purple-50);
      color: var(--purple-800);
    }

    /* ---------- Footer ---------- */
    footer {
      background: var(--purple-900);
      color: rgba(255, 255, 255, 0.62);
      padding: 52px 0 28px;
      position: relative;
      overflow: hidden;
    }

    footer::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: var(--pattern-gold);
      background-size: 72px 72px;
      opacity: 0.5;
    }

    .footer-inner {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: 1.4fr 1fr 1fr;
      gap: 32px;
      padding-bottom: 32px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-brand .brand-text strong { color: var(--white); }
    .footer-brand .brand-text span { color: rgba(255, 255, 255, 0.5); }

    .footer-brand p {
      font-size: 0.88rem;
      max-width: 320px;
      margin: 14px 0 0;
      line-height: 1.7;
    }

    .footer-col h4 {
      color: var(--white);
      font-size: 0.85rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin: 0 0 16px;
    }

    .footer-col ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .footer-col a, .footer-col li {
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .footer-col a:hover { color: var(--white); }
    .footer-col i { width: 16px; color: var(--gold-600); text-align: center; }

    .footer-bottom {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      padding-top: 24px;
      font-size: 0.82rem;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 900px) {
      .hero {
        min-height: 0;
        padding: 36px 0 44px;
      }
      .hero-grid { grid-template-columns: 1fr; gap: 32px; }
      .hero-illustration { order: 2; max-width: 340px; margin: 0 auto; }
      .hero-illustration svg { max-height: none; }
      .hero-text { order: 1; text-align: center; }
      .hero p.lead { margin-left: auto; margin-right: auto; }
      .hero-cta { justify-content: center; }
      .hero-note { justify-content: center; }
      .stat-grid { grid-template-columns: repeat(2, 1fr); }
      .features-grid { grid-template-columns: repeat(2, 1fr); }
      .how-grid { grid-template-columns: 1fr; gap: 32px; }
      .how-grid::before { display: none; }
      .nav-links { display: none; }
      .footer-inner { grid-template-columns: 1fr; }
    }

    @media (max-width: 560px) {
      .features-grid { grid-template-columns: 1fr; }
      .stat-grid { grid-template-columns: 1fr; }
      .stat-item { justify-content: flex-start; }
      .cta-banner { padding: 40px 28px; flex-direction: column; text-align: center; }
      .footer-bottom { flex-direction: column; text-align: center; }
    }

    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      .feature-card, .btn, .navbar { transition: none; }
      .hero-text, .hero-illustration { animation: none; }
    }
  </style>
</head>
<body>

  <div class="top-bar"></div>

  <nav class="navbar" id="navbar">
    <div class="container navbar-inner">
      <a href="/" class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SIPUAS">
        <span class="brand-text">
          <strong>SIPUAS</strong>
          <span>Dinkesda Kab. Purworejo</span>
        </span>
      </a>
      <div class="nav-links">
        <a href="#fitur">Fitur Utama</a>
        <a href="#cara-kerja">Cara Kerja</a>
      </div>
      <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-right-to-bracket"></i>
        Login
      </a>
    </div>
  </nav>

  <header class="hero">
    <div class="container hero-grid">
      <div class="hero-text">
        <span class="eyebrow">
          <i class="fa-solid fa-landmark"></i>
          Sistem Informasi
        </span>
        <h1>Survei Kepuasan Masyarakat <em class="accent">Dinas Kesehatan Daerah</em> Kabupaten Purworejo</h1>
        <p class="lead">
          Susun kuesioner presisi untuk tiap unit, kumpulkan masukan warga melalui QR Code, dan pantau Indeks Kepuasan Masyarakat secara real-time.
        </p>
        <div class="hero-cta">
          <a href="{{ route('login') }}" class="btn btn-primary">
            Masuk Sekarang
            <i class="fa-solid fa-arrow-right"></i>
          </a>
          <a href="#cara-kerja" class="btn btn-outline">Lihat Cara Kerja</a>
        </div>
        <div class="hero-note">
          <i class="fa-solid fa-circle-check"></i>
          <span>Sesuai Permenpan RB No. 14 Tahun 2017 &middot; 9 Unsur Pelayanan</span>
        </div>
      </div>

      <!-- hero-ilustrasi -->
      <div class="hero-illustration">
        <img src="{{ asset('images/ilustrasi.svg') }}" alt="Ilustrasi survei kepuasan masyarakat">
      </div>
    </div>
  </header>

  <div class="stat-strip">
    <div class="container stat-grid">
      <div class="stat-item">
        <i class="fa-solid fa-building-columns"></i>
        <div>
          <strong>1 Dinas Kesehatan</strong>
          <small>Mengawasi seluruh unit layanan</small>
        </div>
      </div>
      <div class="stat-item">
        <i class="fa-solid fa-hospital"></i>
        <div>
          <strong>Puskesmas &amp; RSU</strong>
          <small>Terhubung dalam satu jaringan</small>
        </div>
      </div>
      <div class="stat-item">
        <i class="fa-solid fa-list-check"></i>
        <div>
          <strong>9 Unsur Pelayanan</strong>
          <small>Mengacu Permenpan RB No. 14/2017</small>
        </div>
      </div>
      <div class="stat-item">
        <i class="fa-solid fa-bolt"></i>
        <div>
          <strong>Real-time</strong>
          <small>Nilai IKM terhitung otomatis</small>
        </div>
      </div>
    </div>
  </div>

  <section id="fitur">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">
          <i class="fa-solid fa-star"></i>
          Fitur Unggulan
        </span>
        <h2>Semua kebutuhan pengelolaan SKM dalam satu platform</h2>
        <p>Sistem dirancang modern untuk mempermudah unit kesehatan hingga dinas kesehatan dalam menyusun dan merekap survei.</p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-list-check"></i></div>
          <h3>Kuesioner Fleksibel</h3>
          <p>Atur kuesioner mandiri untuk tiap unit layanan — radio button, rating, atau saran bebas — terhubung ke 9 unsur resmi.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-qrcode"></i></div>
          <h3>Pindai QR Code</h3>
          <p>Responden cukup memindai QR Code yang ditempel pada loket atau meja pelayanan tanpa unduh aplikasi.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
          <h3>Laporan Real-time</h3>
          <p>Indeks Kepuasan Masyarakat (IKM) terhitung otomatis dan siap diekspor dalam format dokumen PDF atau Excel.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
          <h3>Multi-Level Akses</h3>
          <p>Dinas Kesehatan Daerah memantau secara menyeluruh, sementara tiap Puskesmas/RSU mengelola data secara mandiri.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="cara-kerja" class="container">
    <div class="how-wrap">
      <div class="section-head">
        <span class="eyebrow">
          <i class="fa-solid fa-route"></i>
          Alur Kerja
        </span>
        <h2>Mudah digunakan dalam tiga langkah</h2>
      </div>
      <div class="how-grid">
        <div class="how-step">
          <div class="num"><i class="fa-solid fa-qrcode"></i></div>
          <h3>1. Pindai QR Code</h3>
          <p>Masyarakat memindai QR code kuesioner yang tersedia di loket layanan kesehatan.</p>
        </div>
        <div class="how-step">
          <div class="num"><i class="fa-solid fa-star"></i></div>
          <h3>2. Isi Penilaian</h3>
          <p>Memberikan tanggapan &amp; nilai kepuasan sesuai layanan yang baru saja diterima.</p>
        </div>
        <div class="how-step">
          <div class="num"><i class="fa-solid fa-chart-simple"></i></div>
          <h3>3. Hasil Otomatis</h3>
          <p>Nilai IKM langsung terakumulasi pada dashboard sistem secara otomatis.</p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta-banner">
        <div>
          <h2>Siap mengelola SKM secara digital?</h2>
          <p>Login ke sistem untuk mengatur kuesioner, mengunduh QR Code, dan memantau perkembangan nilai IKM unit Anda.</p>
        </div>
        <a href="{{ route('login') }}" class="btn btn-primary">
          Login
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>

  <footer>
    <div class="container">
      <div class="footer-inner">
        <div class="footer-brand">
          <a href="/" class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SIPUAS">
            <span class="brand-text">
              <strong>SIPUAS</strong>
              <span>Survei Kepuasan Masyarakat</span>
            </span>
          </a>
          <p>Sistem digital resmi Dinas Kesehatan Daerah Kabupaten Purworejo untuk memantau mutu layanan kesehatan di seluruh Puskesmas dan RSU.</p>
        </div>
        <div class="footer-col">
          <h4>Navigasi</h4>
          <ul>
            <li><a href="#fitur"><i class="fa-solid fa-chevron-right"></i>Fitur Utama</a></li>
            <li><a href="#cara-kerja"><i class="fa-solid fa-chevron-right"></i>Cara Kerja</a></li>
            <li><a href="{{ route('login') }}"><i class="fa-solid fa-chevron-right"></i>Login</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Jaringan Layanan</h4>
          <ul>
            <li><i class="fa-solid fa-building-columns"></i>Dinkesda Purworejo</li>
            <li><i class="fa-solid fa-hospital"></i>Puskesmas</li>
            <li><i class="fa-solid fa-house-medical"></i>RSUD / RSU</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} SIPUAS &middot; Dinas Kesehatan Daerah Kabupaten Purworejo</span>
        <span>Sesuai Permenpan RB No. 14 Tahun 2017</span>
      </div>
    </div>
  </footer>

  <script>
    const navbar = document.getElementById('navbar');
    if (navbar) {
      window.addEventListener('scroll', () => {
        navbar.classList.toggle('is-scrolled', window.scrollY > 8);
      }, { passive: true });
    }
  </script>

</body>
</html>