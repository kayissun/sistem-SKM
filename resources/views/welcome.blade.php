<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SKM — Survei Kepuasan Masyarakat</title>
  <meta name="description" content="Sistem digital Survei Kepuasan Masyarakat untuk Dinas Kesehatan dan jaringan Puskesmas/RSU — kuesioner fleksibel, isi via QR code, laporan IKM real-time.">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --purple-900: #1e0a45;
      --purple-800: #2E1065;
      --purple-700: #6D28D9;
      --purple-600: #7C3AED;
      --purple-500: #8b5cf6;
      --purple-100: #EDE9FE;
      --purple-50: #f8f6ff;
      --ink: #0f172a;
      --ink-muted: #64748b;
      --accent: #f59e0b;
      --white: #FFFFFF;

      /* GForm/Modern Gradients */
      --gradient-primary: linear-gradient(135deg, #7C3AED 0%, #6D28D9 50%, #4c1d95 100%);
      --gradient-hero-bg: radial-gradient(100% 100% at 50% 0%, #EDE9FE 0%, #f8f6ff 100%);
      --gradient-card: linear-gradient(180deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.6) 100%);
      --gradient-accent: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);

      --radius-lg: 24px;
      --radius-md: 16px;
      --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 20px 25px -5px rgba(109, 40, 217, 0.1), 0 8px 10px -6px rgba(109, 40, 217, 0.05);
    }

    * {
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      margin: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--ink);
      background: var(--purple-50);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    img, svg {
      display: block;
      max-width: 100%;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 24px;
    }

    /* ---------- Header Top Decorative Strip ---------- */
    .top-bar {
      height: 6px;
      background: var(--gradient-primary);
    }

    /* ---------- Navbar ---------- */
    .navbar {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(248, 246, 255, 0.85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(109, 40, 217, 0.08);
    }

    .navbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 24px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 800;
      font-size: 1.2rem;
      color: var(--purple-900);
    }

    .brand-mark {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      background: var(--gradient-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(109, 40, 217, 0.3);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 32px;
      font-weight: 600;
      font-size: 0.95rem;
      color: var(--ink-muted);
    }

    .nav-links a {
      transition: color 0.2s;
    }

    .nav-links a:hover {
      color: var(--purple-700);
    }

    /* ---------- Buttons ---------- */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-weight: 700;
      font-size: 0.95rem;
      border-radius: 99px;
      padding: 14px 28px;
      transition: all 0.25s ease;
      cursor: pointer;
      border: 2px solid transparent;
      white-space: nowrap;
    }

    .btn-primary {
      background: var(--gradient-primary);
      color: var(--white);
      box-shadow: 0 10px 20px -5px rgba(109, 40, 217, 0.4);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 25px -5px rgba(109, 40, 217, 0.5);
    }

    .btn-outline {
      border-color: rgba(109, 40, 217, 0.25);
      color: var(--purple-700);
      background: var(--white);
      box-shadow: var(--shadow-sm);
    }

    .btn-outline:hover {
      border-color: var(--purple-700);
      background: var(--purple-100);
      transform: translateY(-2px);
    }

    .btn-sm {
      padding: 10px 22px;
      font-size: 0.88rem;
    }

    /* ---------- Hero ---------- */
    .hero {
      position: relative;
      padding: 80px 0 64px;
      background: var(--gradient-hero-bg);
      border-bottom: 1px solid rgba(109, 40, 217, 0.05);
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 56px;
      align-items: center;
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--white);
      color: var(--purple-700);
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      padding: 6px 16px;
      border-radius: 99px;
      margin-bottom: 24px;
      border: 1px solid rgba(109, 40, 217, 0.15);
      box-shadow: var(--shadow-sm);
    }

    .eyebrow-badge {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--gradient-accent);
    }

    .hero h1 {
      font-size: clamp(2.2rem, 3.8vw, 3.4rem);
      font-weight: 800;
      line-height: 1.2;
      letter-spacing: -0.02em;
      margin: 0 0 20px;
      color: var(--purple-900);
    }

    .hero h1 .accent {
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero p.lead {
      font-size: 1.1rem;
      color: var(--ink-muted);
      max-width: 520px;
      margin: 0 0 36px;
      font-weight: 400;
    }

    .hero-cta {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 32px;
    }

    .hero-note {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.85rem;
      color: var(--ink-muted);
      font-weight: 500;
    }

    .hero-illustration {
      position: relative;
    }

    .hero-illustration svg {
      filter: drop-shadow(0 20px 30px rgba(109, 40, 217, 0.15));
    }

    /* ---------- Trust strip ---------- */
    .trust {
      padding: 40px 0;
      text-align: center;
    }

    .trust p {
      font-size: 0.8rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--ink-muted);
      font-weight: 700;
      margin-bottom: 20px;
    }

    .trust-items {
      display: flex;
      justify-content: center;
      gap: 48px;
      flex-wrap: wrap;
      color: var(--purple-900);
      font-weight: 700;
      font-size: 1.1rem;
      opacity: 0.7;
    }

    .trust-items span {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ---------- Section Shared ---------- */
    section {
      padding: 80px 0;
    }

    .section-head {
      max-width: 600px;
      margin: 0 auto 56px;
      text-align: center;
    }

    .section-head .eyebrow {
      margin-bottom: 16px;
    }

    .section-head h2 {
      font-size: clamp(1.8rem, 2.8vw, 2.2rem);
      font-weight: 800;
      margin: 0 0 16px;
      color: var(--purple-900);
      letter-spacing: -0.01em;
    }

    .section-head p {
      color: var(--ink-muted);
      margin: 0;
      font-size: 1.05rem;
    }

    /* ---------- Features ---------- */
    .features-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }

    .feature-card {
      background: var(--gradient-card);
      backdrop-filter: blur(8px);
      border-radius: var(--radius-md);
      padding: 32px 24px;
      border: 1px solid rgba(255, 255, 255, 0.8);
      box-shadow: var(--shadow-sm);
      transition: all 0.25s ease;
    }

    .feature-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-lg);
      border-color: rgba(109, 40, 217, 0.2);
    }

    .feature-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: var(--gradient-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 8px 16px -4px rgba(109, 40, 217, 0.3);
    }

    .feature-icon svg {
      stroke: var(--white);
    }

    .feature-card h3 {
      font-size: 1.1rem;
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
    .how {
      background: var(--white);
      border-radius: 36px;
      margin: 40px 0;
      box-shadow: var(--shadow-sm);
    }

    .how-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;
      position: relative;
    }

    .how-step {
      text-align: center;
      padding: 16px;
    }

    .how-step .num {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--gradient-primary);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.25rem;
      margin: 0 auto 24px;
      box-shadow: 0 10px 20px -5px rgba(109, 40, 217, 0.35);
    }

    .how-step h3 {
      font-size: 1.1rem;
      font-weight: 700;
      margin: 0 0 10px;
      color: var(--purple-900);
    }

    .how-step p {
      font-size: 0.92rem;
      color: var(--ink-muted);
      margin: 0;
    }

    /* ---------- CTA Banner ---------- */
    .cta-banner {
      background: var(--gradient-primary);
      border-radius: var(--radius-lg);
      padding: 64px 56px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 32px;
      flex-wrap: wrap;
      color: var(--white);
      box-shadow: var(--shadow-lg);
      position: relative;
      overflow: hidden;
    }

    .cta-banner::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -20%;
      width: 300px;
      height: 300px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      pointer-events: none;
    }

    .cta-banner h2 {
      font-size: 1.8rem;
      font-weight: 800;
      margin: 0 0 12px;
    }

    .cta-banner p {
      color: rgba(255, 255, 255, 0.85);
      margin: 0;
      max-width: 480px;
      font-size: 1.05rem;
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
      color: rgba(255, 255, 255, 0.7);
      padding: 48px 0 32px;
    }

    .footer-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .footer-inner .brand {
      color: var(--white);
    }

    footer small {
      font-size: 0.85rem;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 900px) {
      .hero-grid {
        grid-template-columns: 1fr;
        gap: 40px;
      }
      .hero-illustration {
        order: 2;
        max-width: 420px;
        margin: 0 auto;
      }
      .hero-text {
        order: 1;
        text-align: center;
      }
      .hero p.lead {
        margin-left: auto;
        margin-right: auto;
      }
      .hero-cta {
        justify-content: center;
      }
      .hero-note {
        justify-content: center;
      }
      .features-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .how-grid {
        grid-template-columns: 1fr;
        gap: 32px;
      }
      .nav-links {
        display: none;
      }
    }

    @media (max-width: 560px) {
      .features-grid {
        grid-template-columns: 1fr;
      }
      .cta-banner {
        padding: 40px 28px;
        flex-direction: column;
        text-align: center;
      }
      .hero {
        padding: 48px 0 40px;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      html {
        scroll-behavior: auto;
      }
      .feature-card, .btn {
        transition: none;
      }
    }
  </style>
</head>
<body>

  <div class="top-bar"></div>

  <nav class="navbar">
    <div class="container navbar-inner">
      <a href="/" class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SKM Digital" width="40" height="50">
        SIPUAS
      </a>
      <div class="nav-links">
        <a href="#fitur">Fitur Utama</a>
        <a href="#cara-kerja">Cara Kerja</a>
      </div>
      <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk ke Sistem</a>
    </div>
  </nav>

  <header class="hero">
    <div class="container hero-grid">
      <div class="hero-text">
        <span class="eyebrow">
          <span class="eyebrow-badge"></span>
          Sistem SKM Digital
        </span>
        <h1>Sistem Informasi Kepuasan Masyarakat <span class="accent">Dinas Kesehatan Daerah</span> Kabupaten Purworejo</h1>
        <p class="lead">
          Susun kuesioner presisi untuk tiap unit, kumpulkan masukan warga melalui QR Code, dan pantau Indeks Kepuasan Masyarakat secara real-time.
        </p>
        <div class="hero-cta">
          <a href="{{ route('login') }}" class="btn btn-primary">
            Masuk ke Sistem
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
              <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </a>
          <a href="#cara-kerja" class="btn btn-outline">Lihat Cara Kerja</a>
        </div>
        <div class="hero-note">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>Sesuai Permenpan RB No. 14 Tahun 2017 &middot; 9 Unsur Pelayanan</span>
        </div>
      </div>

      <div class="hero-illustration">
        <!-- Form Illustration Style Google Forms / Modern Digital Survey -->
        <img src="{{ asset('images/hero-illustration.svg') }}" alt="ilustrasi">
          <defs>
            <linearGradient id="gformHeader" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#7C3AED" />
              <stop offset="100%" stop-color="#4C1D95" />
            </linearGradient>
            <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
              <feDropShadow dx="0" dy="12" stdDeviation="16" flood-color="#6D28D9" flood-opacity="0.12" />
            </filter>
          </defs>

          <circle cx="260" cy="240" r="210" fill="#EDE9FE" opacity="0.6" />
          <circle cx="450" cy="80" r="28" fill="#FDBA3B" opacity="0.3" />

          <!-- Card Kuesioner (Satu Tampilan Google Form Modern) -->
          <g filter="url(#shadow)">
            <rect x="110" y="60" width="300" height="360" rx="20" fill="#FFFFFF" />

            <!-- Form Top Accent Stripe -->
            <rect x="110" y="60" width="300" height="14" rx="7" fill="url(#gformHeader)" />
            <rect x="110" y="68" width="300" height="6" fill="url(#gformHeader)" />

            <!-- Form Header Section -->
            <rect x="134" y="94" width="140" height="14" rx="4" fill="#2E1065" />
            <rect x="134" y="116" width="220" height="8" rx="3" fill="#94A3B8" />
            <line x1="134" y1="138" x2="386" y2="138" stroke="#F1F5F9" stroke-width="2" />

            <!-- Item Question 1 -->
            <rect x="134" y="156" width="180" height="10" rx="3" fill="#475569" />
            <circle cx="144" cy="182" r="8" fill="#EDE9FE" stroke="#7C3AED" stroke-width="2" />
            <circle cx="144" cy="182" r="4" fill="#7C3AED" />
            <rect x="160" y="178" width="100" height="8" rx="3" fill="#64748B" />
            <circle cx="144" cy="204" r="8" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="2" />
            <rect x="160" y="200" width="80" height="8" rx="3" fill="#94A3B8" />

            <!-- Item Question 2 (Rating Stars) -->
            <rect x="134" y="232" width="160" height="10" rx="3" fill="#475569" />
            <g transform="translate(134,252)">
              <path d="M10 0l2.9 6 6.6 0.9-4.8 4.7 1.1 6.6-5.8-3.1-5.8 3.1 1.1-6.6-4.8-4.7 6.6-0.9z" fill="#FDBA3B" />
              <path d="M36 0l2.9 6 6.6 0.9-4.8 4.7 1.1 6.6-5.8-3.1-5.8 3.1 1.1-6.6-4.8-4.7 6.6-0.9z" fill="#FDBA3B" />
              <path d="M62 0l2.9 6 6.6 0.9-4.8 4.7 1.1 6.6-5.8-3.1-5.8 3.1 1.1-6.6-4.8-4.7 6.6-0.9z" fill="#FDBA3B" />
              <path d="M88 0l2.9 6 6.6 0.9-4.8 4.7 1.1 6.6-5.8-3.1-5.8 3.1 1.1-6.6-4.8-4.7 6.6-0.9z" fill="#FDBA3B" />
              <path d="M114 0l2.9 6 6.6 0.9-4.8 4.7 1.1 6.6-5.8-3.1-5.8 3.1 1.1-6.6-4.8-4.7 6.6-0.9z" fill="#E2E8F0" />
            </g>

            <!-- Submit Button Placeholder -->
            <rect x="134" y="296" width="88" height="32" rx="16" fill="url(#gformHeader)" />
            <rect x="154" y="308" width="48" height="8" rx="3" fill="#FFFFFF" />
          </g>

          <!-- Floating Check Badge -->
          <g filter="url(#shadow)">
            <circle cx="390" cy="330" r="32" fill="#FFFFFF" />
            <circle cx="390" cy="330" r="24" fill="#10B981" />
            <path d="M382 330l6 6 12-12" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
          </g>

          <!-- Floating QR Code Badge -->
          <g filter="url(#shadow)">
            <rect x="80" y="260" width="72" height="72" rx="16" fill="#FFFFFF" />
            <rect x="92" y="272" width="20" height="20" rx="4" fill="#2E1065" />
            <rect x="120" y="272" width="20" height="8" rx="2" fill="#7C3AED" />
            <rect x="120" y="284" width="8" height="20" rx="2" fill="#2E1065" />
            <rect x="92" y="300" width="20" height="20" rx="4" fill="#7C3AED" />
            <rect x="120" y="308" width="20" height="12" rx="2" fill="#2E1065" />
          </g>
        </svg>
      </div>
    </div>
  </header>

  <div class="trust">
    <div class="container">
      <p>Digunakan untuk pengawasan mutu layanan kesehatan terpadu</p>
      <div class="trust-items">
        <span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" />
          </svg>
          Dinkesda Purworejo
        </span>
        <span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" />
          </svg>
          Puskesmas
        </span>
        <span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" />
          </svg>
          RSUD / RSU
        </span>
      </div>
    </div>
  </div>

  <section id="fitur">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">
          <span class="eyebrow-badge"></span>
          Fitur Unggulan
        </span>
        <h2>Semua kebutuhan pengelolaan SKM dalam satu platform</h2>
        <p>Sistem dirancang modern untuk mempermudah unit kesehatan hingga dinas kesehatan dalam menyusun dan merekap survei.</p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <h3>Kuesioner Fleksibel</h3>
          <p>Atur kuesioner mandiri untuk tiap unit layanan — radio button, rating, atau saran bebas — terhubung ke 9 unsur resmi.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M12 4v1m6 11h2m-6 0h-2v4m0-6v-3m6 0.343V11a6 6 0 10-12 0v.343m12 0A5.98 5.98 0 0012 9a5.98 5.98 0 00-6 2.343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <h3>Pindai QR Code</h3>
          <p>Responden cukup memindai QR Code yang ditempel pada loket atau meja pelayanan tanpa unduh aplikasi.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <h3>Laporan Real-time</h3>
          <p>Indeks Kepuasan Masyarakat (IKM) terhitung otomatis dan siap diekspor dalam format dokumen PDF atau Excel.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <h3>Multi-Level Akses</h3>
          <p>Dinas Kesehatan Daerah memantau secara menyeluruh, sementara tiap Puskesmas/RSU mengelola data secara mandiri.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="cara-kerja" class="container">
    <div class="how">
      <div class="section-head" style="padding-top: 48px;">
        <span class="eyebrow">
          <span class="eyebrow-badge"></span>
          Alur Kerja
        </span>
        <h2>Mudah digunakan dalam tiga langkah</h2>
      </div>
      <div class="how-grid" style="padding: 0 32px 48px;">
        <div class="how-step">
          <div class="num">1</div>
          <h3>Pindai QR Code</h3>
          <p>Masyarakat memindai QR code kuesioner yang tersedia di loket layanan kesehatan.</p>
        </div>
        <div class="how-step">
          <div class="num">2</div>
          <h3>Isi Penilaian</h3>
          <p>Memberikan tanggapan & nilai kepuasan sesuai layanan yang baru saja diterima.</p>
        </div>
        <div class="how-step">
          <div class="num">3</div>
          <h3>Hasil Otomatis</h3>
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
          <p>Masuk ke sistem untuk mengatur kuesioner, mengunduh QR Code, dan memantau perkembangan nilai IKM unit Anda.</p>
        </div>
        <a href="{{ route('login') }}" class="btn btn-primary">
          Masuk ke Sistem
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </a>
      </div>
    </div>
  </section>

  <footer>
    <div class="container footer-inner">
      <a href="/" class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SKM Digital" width="40" height="50">
        SIPUAS
      </a>
      <small>&copy; {{ date('Y') }} Sistem Kepuasan Masyarakat &middot; Dinkesda Purworejo</small>
    </div>
  </footer>

</body>
</html>