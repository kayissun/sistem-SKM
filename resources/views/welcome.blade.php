<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SKM — Survei Kepuasan Masyarakat</title>
    <meta name="description" content="Sistem digital Survei Kepuasan Masyarakat untuk Dinas Kesehatan dan jaringan Puskesmas/RSU — kuesioner fleksibel, isi via QR code, laporan IKM real-time.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple-900: #2E1065;
            --purple-700: #6D28D9;
            --purple-600: #7C3AED;
            --purple-500: #9061F9;
            --purple-100: #EDE9FE;
            --purple-50: #F7F5FF;
            --ink: #1E1B2E;
            --ink-muted: #675F7A;
            --accent: #FDBA3B;
            --white: #FFFFFF;
            --radius-lg: 28px;
            --radius-md: 16px;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--ink);
            background: var(--purple-50);
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }
        img, svg { display: block; max-width: 100%; }
        .container { max-width: 1160px; margin: 0 auto; padding: 0 24px; }

        /* ---------- Navbar ---------- */
        .navbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(247, 245, 255, 0.85);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(109, 40, 217, 0.08);
        }
        .navbar-inner { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1.15rem; }
        .brand-mark {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--purple-600), var(--purple-900));
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .nav-links { display: flex; align-items: center; gap: 28px; font-weight: 500; font-size: 0.95rem; color: var(--ink-muted); }
        .nav-links a:hover { color: var(--purple-700); }

        /* ---------- Buttons ---------- */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; font-size: 0.95rem; border-radius: 999px; padding: 13px 28px; transition: transform .15s ease, box-shadow .15s ease, background .15s ease; cursor: pointer; border: 2px solid transparent; white-space: nowrap; }
        .btn-primary { background: var(--purple-700); color: var(--white); box-shadow: 0 10px 24px -8px rgba(109,40,217,0.55); }
        .btn-primary:hover { background: var(--purple-600); transform: translateY(-1px); }
        .btn-outline { border-color: var(--purple-700); color: var(--purple-700); background: transparent; }
        .btn-outline:hover { background: var(--purple-100); }
        .btn-sm { padding: 9px 20px; font-size: 0.85rem; }

        /* ---------- Hero ---------- */
        .hero { padding: 76px 0 64px; }
        .hero-grid { display: grid; grid-template-columns: 1.05fr 0.95fr; gap: 48px; align-items: center; }
        .eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--purple-100); color: var(--purple-700);
            font-size: 0.78rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
            padding: 7px 16px; border-radius: 999px; margin-bottom: 22px;
        }
        .eyebrow .dots { display: inline-flex; gap: 3px; }
        .eyebrow .dots span { width: 5px; height: 5px; border-radius: 50%; background: var(--purple-700); }
        .eyebrow .dots span:nth-child(1) { opacity: 1; }
        .eyebrow .dots span:nth-child(2) { opacity: 0.75; }
        .eyebrow .dots span:nth-child(3) { opacity: 0.5; }
        .eyebrow .dots span:nth-child(4) { opacity: 0.3; }

        .hero h1 { font-size: clamp(2.1rem, 3.6vw, 3.15rem); font-weight: 800; line-height: 1.15; letter-spacing: -0.01em; margin: 0 0 20px; }
        .hero h1 .accent { color: var(--purple-700); }
        .hero p.lead { font-size: 1.05rem; color: var(--ink-muted); max-width: 480px; margin: 0 0 32px; }
        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 36px; }
        .hero-note { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: var(--ink-muted); }

        .hero-illustration { position: relative; }

        /* ---------- Trust strip ---------- */
        .trust { padding: 8px 0 56px; text-align: center; }
        .trust p { font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase; color: var(--ink-muted); font-weight: 600; margin-bottom: 18px; }
        .trust-items { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; color: var(--purple-900); font-weight: 700; font-size: 1.05rem; opacity: 0.55; }

        /* ---------- Section shared ---------- */
        section { padding: 64px 0; }
        .section-head { max-width: 560px; margin: 0 auto 48px; text-align: center; }
        .section-head .eyebrow { margin-bottom: 16px; }
        .section-head h2 { font-size: clamp(1.6rem, 2.6vw, 2.1rem); font-weight: 800; margin: 0 0 12px; }
        .section-head p { color: var(--ink-muted); margin: 0; }

        /* ---------- Features ---------- */
        .features-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
        .feature-card { background: var(--white); border-radius: var(--radius-md); padding: 28px 24px; border: 1px solid rgba(109,40,217,0.08); transition: transform .15s ease, box-shadow .15s ease; }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -24px rgba(46,16,101,0.35); }
        .feature-icon { width: 46px; height: 46px; border-radius: 12px; background: var(--purple-100); display: flex; align-items: center; justify-content: center; margin-bottom: 18px; }
        .feature-card h3 { font-size: 1.02rem; font-weight: 700; margin: 0 0 8px; }
        .feature-card p { font-size: 0.9rem; color: var(--ink-muted); margin: 0; }

        /* ---------- How it works ---------- */
        .how { background: var(--white); }
        .how-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; position: relative; }
        .how-step { text-align: center; padding: 0 12px; }
        .how-step .num {
            width: 52px; height: 52px; border-radius: 50%; background: var(--purple-700); color: var(--white);
            display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.15rem;
            margin: 0 auto 18px;
        }
        .how-step h3 { font-size: 1rem; font-weight: 700; margin: 0 0 8px; }
        .how-step p { font-size: 0.88rem; color: var(--ink-muted); margin: 0; }

        /* ---------- CTA banner ---------- */
        .cta-banner {
            background: linear-gradient(135deg, var(--purple-700), var(--purple-900));
            border-radius: var(--radius-lg);
            padding: 56px 48px;
            display: flex; align-items: center; justify-content: space-between; gap: 32px; flex-wrap: wrap;
            color: var(--white);
        }
        .cta-banner h2 { font-size: 1.6rem; font-weight: 800; margin: 0 0 10px; }
        .cta-banner p { color: rgba(255,255,255,0.8); margin: 0; max-width: 420px; }
        .cta-banner .btn-primary { background: var(--white); color: var(--purple-700); box-shadow: none; }
        .cta-banner .btn-primary:hover { background: var(--purple-100); }

        /* ---------- Footer ---------- */
        footer { background: var(--purple-900); color: rgba(255,255,255,0.7); padding: 40px 0 28px; margin-top: 40px; }
        .footer-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-inner .brand { color: var(--white); }
        footer small { font-size: 0.82rem; }

        /* ---------- Responsive ---------- */
        @media (max-width: 900px) {
            .hero-grid { grid-template-columns: 1fr; }
            .hero-illustration { order: 2; max-width: 380px; margin: 0 auto; }
            .hero-text { order: 1; }
            .hero p.lead { max-width: none; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .how-grid { grid-template-columns: 1fr; gap: 40px; }
            .nav-links { display: none; }
        }
        @media (max-width: 560px) {
            .features-grid { grid-template-columns: 1fr; }
            .cta-banner { padding: 40px 28px; flex-direction: column; text-align: center; }
            .hero { padding: 48px 0 40px; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .feature-card, .btn-primary { transition: none; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container navbar-inner">
        <a href="/" class="brand">
            <span class="brand-mark">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            SKM Digital
        </a>
        <div class="nav-links">
            <a href="#fitur">Fitur</a>
            <a href="#cara-kerja">Cara Kerja</a>
        </div>
        <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk ke Sistem</a>
    </div>
</nav>

<header class="hero">
    <div class="container hero-grid">
        <div class="hero-text">
            <span class="eyebrow">
                <span class="dots"><span></span><span></span><span></span><span></span></span>
                Sistem SKM Digital
            </span>
            <h1>Survei kepuasan masyarakat, dari <span class="accent">Puskesmas</span> sampai <span class="accent">Dinas Kesehatan</span>, dalam satu sistem.</h1>
            <p class="lead">
                Susun kuesioner sesuai kebutuhan tiap unit, kumpulkan penilaian warga cukup lewat
                pindai QR code, dan pantau Indeks Kepuasan Masyarakat secara real-time —
                tanpa rekap manual lagi.
            </p>
            <div class="hero-cta">
                <a href="{{ route('login') }}" class="btn btn-primary">Masuk ke Sistem</a>
                <a href="#cara-kerja" class="btn btn-outline">Lihat Cara Kerja</a>
            </div>
            <div class="hero-note">
                <span>Sesuai Permenpan RB No. 14 Tahun 2017 &middot; 9 unsur pelayanan resmi</span>
            </div>
        </div>

        <div class="hero-illustration">
            <!-- Ilustrasi orisinal bergaya flat illustration (mirip storyset), tema survei/checklist -->
            <svg viewBox="0 0 520 480" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustrasi orang mengisi survei kepuasan">
                <circle cx="270" cy="240" r="210" fill="#EDE9FE"/>
                <circle cx="120" cy="90" r="34" fill="#F7F5FF"/>
                <circle cx="460" cy="360" r="46" fill="#F7F5FF"/>

                <!-- kartu kuesioner -->
                <rect x="130" y="86" width="260" height="330" rx="24" fill="#FFFFFF"/>
                <rect x="130" y="86" width="260" height="58" rx="24" fill="#6D28D9"/>
                <rect x="130" y="118" width="260" height="26" fill="#6D28D9"/>
                <circle cx="156" cy="115" r="6" fill="#FFFFFF" opacity="0.9"/>
                <circle cx="176" cy="115" r="6" fill="#FFFFFF" opacity="0.6"/>
                <circle cx="196" cy="115" r="6" fill="#FFFFFF" opacity="0.4"/>
                <rect x="330" y="106" width="36" height="18" rx="9" fill="#FFFFFF" opacity="0.25"/>

                <!-- checklist rows -->
                <g>
                    <rect x="156" y="168" width="20" height="20" rx="6" fill="#6D28D9"/>
                    <path d="M162 178l4 4 8-8" stroke="#FFFFFF" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <rect x="188" y="173" width="160" height="10" rx="5" fill="#E4DEFB"/>
                </g>
                <g>
                    <rect x="156" y="206" width="20" height="20" rx="6" fill="#6D28D9"/>
                    <path d="M162 216l4 4 8-8" stroke="#FFFFFF" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <rect x="188" y="211" width="140" height="10" rx="5" fill="#E4DEFB"/>
                </g>
                <g>
                    <rect x="156" y="244" width="20" height="20" rx="6" fill="#FFFFFF" stroke="#C9BCF7" stroke-width="2"/>
                    <rect x="188" y="249" width="150" height="10" rx="5" fill="#E4DEFB"/>
                </g>
                <g>
                    <rect x="156" y="282" width="20" height="20" rx="6" fill="#FFFFFF" stroke="#C9BCF7" stroke-width="2"/>
                    <rect x="188" y="287" width="120" height="10" rx="5" fill="#E4DEFB"/>
                </g>

                <!-- skala rating 1-4 -->
                <g transform="translate(156,330)">
                    <path d="M14 0l4.1 8.6 9.4 1.3-6.8 6.7 1.6 9.4L14 21.4 5.7 26l1.6-9.4L0.5 9.9l9.4-1.3z" fill="#FDBA3B"/>
                    <path d="M54 0l4.1 8.6 9.4 1.3-6.8 6.7 1.6 9.4L54 21.4 45.7 26l1.6-9.4-6.8-6.7 9.4-1.3z" fill="#FDBA3B"/>
                    <path d="M94 0l4.1 8.6 9.4 1.3-6.8 6.7 1.6 9.4L94 21.4 85.7 26l1.6-9.4-6.8-6.7 9.4-1.3z" fill="#FDBA3B"/>
                    <path d="M134 0l4.1 8.6 9.4 1.3-6.8 6.7 1.6 9.4L134 21.4l-8.3 4.6 1.6-9.4-6.8-6.7 9.4-1.3z" fill="#E4DEFB"/>
                </g>

                <!-- orang -->
                <g transform="translate(300,230)">
                    <ellipse cx="60" cy="222" rx="54" ry="12" fill="#DDD6FE" opacity="0.6"/>
                    <rect x="24" y="96" width="72" height="104" rx="30" fill="#6D28D9"/>
                    <rect x="10" y="120" width="26" height="70" rx="13" fill="#6D28D9" transform="rotate(-18 23 120)"/>
                    <rect x="86" y="70" width="26" height="80" rx="13" fill="#7C3AED" transform="rotate(18 99 70)"/>
                    <circle cx="108" cy="58" r="14" fill="#FDBA3B"/>
                    <path d="M108 48l3.3 6.9 7.6 1.1-5.5 5.4 1.3 7.6-6.7-3.5-6.7 3.5 1.3-7.6-5.5-5.4 7.6-1.1z" fill="#FFFFFF"/>
                    <rect x="34" y="182" width="20" height="46" rx="10" fill="#4C1D8F"/>
                    <rect x="66" y="182" width="20" height="46" rx="10" fill="#4C1D8F"/>
                    <circle cx="60" cy="70" r="34" fill="#F6C9A0"/>
                    <path d="M28 62c0-20 16-34 32-34s32 14 32 34c0-8-6-16-14-18-6 8-18 10-30 8-8 10-20 8-20 10z" fill="#2E1065"/>
                    <path d="M46 74c2 6 8 10 14 10s12-4 14-10" stroke="#8A5A2E" stroke-width="2.2" stroke-linecap="round" fill="none" opacity="0.5"/>
                </g>

                <!-- elemen dekoratif -->
                <circle cx="108" cy="120" r="7" fill="#FDBA3B" opacity="0.8"/>
                <circle cx="404" cy="150" r="10" fill="#7C3AED" opacity="0.35"/>
                <circle cx="150" cy="400" r="8" fill="#7C3AED" opacity="0.3"/>
                <circle cx="420" cy="330" r="6" fill="#FDBA3B" opacity="0.7"/>
            </svg>
        </div>
    </div>
</header>

<div class="trust">
    <div class="container">
        <p>Dipakai untuk pengawasan mutu layanan kesehatan</p>
        <div class="trust-items">
            <span>Dinas Kesehatan</span>
            <span>Puskesmas</span>
            <span>Rumah Sakit Umum</span>
        </div>
    </div>
</div>

<section id="fitur">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">
                <span class="dots"><span></span><span></span><span></span><span></span></span>
                Fitur utama
            </span>
            <h2>Semua yang dibutuhkan untuk mengelola SKM</h2>
            <p>Dari penyusunan kuesioner sampai laporan resmi, satu sistem untuk semua unit layanan.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M9 11l3 3L22 4M12 5H5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-7" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <h3>Kuesioner Fleksibel</h3>
                <p>Tiap unit menyusun pertanyaannya sendiri — radio, dropdown, atau teks bebas — tetap terhubung ke 9 unsur pelayanan resmi.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke="#6D28D9" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke="#6D28D9" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke="#6D28D9" stroke-width="2"/><path d="M14 17h7M17 14v7" stroke="#6D28D9" stroke-width="2" stroke-linecap="round"/></svg>
                </span>
                <h3>Isi via QR Code</h3>
                <p>Warga cukup pindai kode QR di loket layanan untuk mengisi survei — tanpa instal aplikasi apa pun.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 19V10M12 19V5M20 19v-7" stroke="#6D28D9" stroke-width="2" stroke-linecap="round"/></svg>
                </span>
                <h3>Laporan Real-time</h3>
                <p>Indeks Kepuasan Masyarakat terhitung otomatis sesuai Permenpan RB, siap diunduh sebagai PDF atau Excel.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <h3>Multi-Level Akses</h3>
                <p>Dinas Kesehatan mengawasi semua unit, tiap Puskesmas/RSU mengelola datanya sendiri secara mandiri.</p>
            </div>
        </div>
    </div>
</section>

<section id="cara-kerja" class="how">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">
                <span class="dots"><span></span><span></span><span></span><span></span></span>
                Cara kerja
            </span>
            <h2>Tiga langkah, dari loket sampai laporan</h2>
        </div>
        <div class="how-grid">
            <div class="how-step">
                <div class="num">1</div>
                <h3>Pindai atau buka tautan</h3>
                <p>Warga memindai QR code atau membuka tautan survei yang ditempel di loket layanan.</p>
            </div>
            <div class="how-step">
                <div class="num">2</div>
                <h3>Isi penilaian</h3>
                <p>Menjawab kuesioner sesuai pengalaman layanan yang baru diterima, hanya perlu waktu singkat.</p>
            </div>
            <div class="how-step">
                <div class="num">3</div>
                <h3>Laporan otomatis</h3>
                <p>Nilai IKM terhitung otomatis dan langsung terlihat di dashboard unit maupun Dinas Kesehatan.</p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="cta-banner">
            <div>
                <h2>Siap kelola SKM unit Anda?</h2>
                <p>Masuk ke sistem untuk mengatur kuesioner, memantau laporan, dan membagikan QR code survei.</p>
            </div>
            <a href="{{ route('login') }}" class="btn btn-primary">Masuk ke Sistem</a>
        </div>
    </div>
</section>

<footer>
    <div class="container footer-inner">
        <a href="/" class="brand">
            <span class="brand-mark">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            SKM Digital
        </a>
        <small>&copy; {{ date('Y') }} Sistem Survei Kepuasan Masyarakat. Sesuai Permenpan RB No. 14 Tahun 2017.</small>
    </div>
</footer>

</body>
</html>
