<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Puskesmas') - SKM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">

    <style>
        :root {
            --sp-purple-900: #180733;
            --sp-purple-800: #2E1065;
            --sp-purple-700: #6D28D9;
            --sp-purple-600: #7C3AED;
            --sp-purple-100: #EDE9FE;
            --sp-purple-50:  #FAF8FF;
            --sp-gold-600:   #C88719;
            --sp-gold-400:   #E4A63B;
            --sp-gold-100:   #FCF1DC;
            --sp-ink:        #14102B;
            --sp-ink-muted:  #625B78;
            --sp-sidebar-w:  260px;

            /* Alias tanpa prefix (biar kompatibel dgn dashboard/section views) */
            --purple-900:  #180733;
            --purple-800:  #2E1065;
            --purple-700:  #6D28D9;
            --purple-600:  #7C3AED;
            --purple-500:  #8B5CF6;
            --purple-100:  #EDE9FE;
            --purple-50:   #FAF8FF;
            --gold-700:    #A66A0E;
            --gold-600:    #C88719;
            --gold-400:    #E4A63B;
            --gold-100:    #FCF1DC;
            --ink:         #14102B;
            --ink-muted:   #625B78;
            --surface-0:   #FFFFFF;
            --surface-1:   #FAF8FF;
            --gradient-primary: linear-gradient(135deg, #7C3AED 0%, #5B21B6 55%, #2A0B5E 100%);
            --shadow-sm:  0 1px 3px rgba(24,7,51,.04);
            --shadow-md:  0 4px 12px rgba(24,7,51,.08);
            --bs-primary: #6D28D9;
            --bs-primary-rgb: 109, 40, 217;
            --bs-link-color: #6D28D9;
            --bs-link-hover-color: #2E1065;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--surface-1);
            color: var(--ink);
            font-size: .9rem;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }

        a { text-decoration: none; }

        /* ---------- Bootstrap component reskin ---------- */
        .btn { border-radius: 10px; font-weight: 600; font-size: .84rem; }
        .btn-primary {
            background: var(--sp-purple-700);
            border: none;
            box-shadow: 0 8px 16px -6px rgba(46,16,101,.4);
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background: var(--sp-purple-700);
            box-shadow: 0 8px 16px -6px rgba(46,16,101,.4);
        }
        .btn-outline-primary { color: var(--purple-700); border-color: rgba(109,40,217,.3); }
        .btn-outline-primary:hover { background: var(--purple-100); color: var(--purple-800); border-color: var(--purple-700); }
        .btn-outline-light { border-color: rgba(255,255,255,.35); }

        .card {
            border: 1px solid rgba(24,7,51,.06);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
        }
        .card-header {
            background: var(--surface-1);
            border-bottom: 1px solid rgba(24,7,51,.06);
            font-weight: 700;
            color: var(--purple-900);
            font-size: .88rem;
            border-radius: 14px 14px 0 0 !important;
            padding: 13px 20px;
        }

        .table thead th {
            background: var(--surface-1);
            color: var(--ink-muted);
            font-weight: 700;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 1px solid rgba(24,7,51,.06);
        }
        .table { border-radius: 12px; overflow: hidden; font-size: .84rem; }
        .table td { vertical-align: middle; }

        .alert { border-radius: 12px; border-width: 1px; font-size: .85rem; }
        .alert-success { background: #ECFDF5; border-color: #A7F3D0; color: #065F46; }
        .alert-danger  { background: #FEF2F2; border-color: #FECACA; color: #991B1B; }

        .badge.bg-primary { background: var(--sp-purple-700) !important; }

        /* ---------- Utility warna (putih-ungu-emas) ---------- */
        .text-purple { color: var(--sp-purple-700) !important; }
        .text-gold { color: var(--sp-gold-600) !important; }
        .bg-purple-soft { background: var(--sp-purple-50) !important; }
        .bg-gold {
            background: linear-gradient(135deg, #C88719 0%, #E4A63B 100%) !important;
            color: #fff !important;
            border: none !important;
        }
        .border-purple-soft { border-color: rgba(109,40,217,.18) !important; }
        .btn-gold {
            background: linear-gradient(135deg, #C88719 0%, #E4A63B 100%);
            border: none; color: #fff;
            box-shadow: 0 8px 16px -6px rgba(166,106,14,.45);
        }
        .btn-gold:hover, .btn-gold:focus { background: linear-gradient(135deg, #B07712 0%, #D89A29 100%); color: #fff; }
        .btn-outline-gold {
            color: var(--sp-gold-600); border-color: #E4A63B;
            background: var(--sp-gold-100);
        }
        .btn-outline-gold:hover { background: var(--sp-gold-600); color: #fff; border-color: var(--sp-gold-600); }

        .form-select {
            border-color: #D4D0E8;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236D28D9' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            background-size: 16px 12px;
        }
        .form-select:hover { border-color: #A78BFA; }
        .form-control:hover { border-color: #A78BFA; }

        .form-control:focus,
        .form-select:focus {
            border-color: #7C3AED !important;
            box-shadow: 0 0 0 .2rem rgba(124,58,237,.15) !important;
            outline: none;
        }

        .form-check-input:checked {
            background-color: #6D28D9;
            border-color: #6D28D9;
        }
        .form-check-input:focus {
            border-color: #A78BFA;
            box-shadow: 0 0 0 .2rem rgba(109, 40, 217, .15);
        }

        /* --- Opsi dropdown: highlight ungu (bukan biru default browser) --- */
        select option {
            background-color: #FFFFFF;
            color: #180733;
            padding: .5rem;
        }
        select option:hover,
        select option:focus,
        select option:checked,
        select option:selected {
            background-color: #EDE9FE;
            color: #6D28D9;
            font-weight: 600;
        }

        /* ---------- Sidebar ---------- */
        .sp-sidebar {
            position: fixed;
            top: 0; bottom: 0; left: 0;
            width: var(--sp-sidebar-w);
            background: linear-gradient(180deg, #2A0B5E 0%, #180733 100%);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transform: translateX(0);
            transition: transform .25s ease;
        }

        .sp-sidebar__brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 22px 20px;
        }
        .sp-sidebar__brand img { width: 34px; height: auto; }
        .sp-sidebar__brand .name { color: #fff; font-weight: 800; font-size: 1rem; line-height: 1.2; }
        .sp-sidebar__brand .role { color: rgba(255,255,255,.7); font-size: .7rem; font-weight: 600; letter-spacing: .02em; }

        .sp-nav {
            flex: 1;
            overflow-y: auto;
            padding: 6px 14px 14px;
        }
        .sp-nav .sp-label {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            margin: 18px 12px 8px;
        }
        .sp-nav a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 14px;
            border-radius: 10px;
            color: rgba(255,255,255,.65);
            font-weight: 600;
            font-size: .84rem;
            margin-bottom: 2px;
            transition: background .15s, color .15s;
        }
        .sp-nav a i { width: 18px; text-align: center; font-size: .88rem; }
        .sp-nav a:hover { background: rgba(255,255,255,.07); color: #fff; }
        .sp-nav a.active {
            background: rgba(255,255,255,.1);
            color: #fff;
            box-shadow: inset 3px 0 0 var(--sp-gold-400);
        }
        .sp-nav a.sp-switch {
            background: rgba(228,166,59,.12);
            color: #F5D28A;
            box-shadow: inset 3px 0 0 var(--sp-gold-400);
        }
        .sp-nav a.sp-switch:hover { background: rgba(228,166,59,.22); color: #fff; }

        .sp-sidebar__foot {
            padding: 14px 22px 20px;
            border-top: 1px solid rgba(255,255,255,.07);
            color: rgba(255,255,255,.35);
            font-size: .68rem;
        }

        /* ---------- Header + content ---------- */
        .sp-main {
            margin-left: var(--sp-sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sp-header {
            position: sticky;
            top: 0;
            z-index: 30;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(24,7,51,.06);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .sp-header h1 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--purple-900);
            margin: 0;
            letter-spacing: -.01em;
        }

        .sp-toggle {
            display: none;
            border: none;
            background: var(--purple-50);
            color: var(--purple-700);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .sp-user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(24,7,51,.08);
            background: #fff;
            border-radius: 99px;
            padding: 5px 14px 5px 5px;
            cursor: pointer;
        }
        .sp-user-btn .avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .78rem;
        }
        .sp-user-btn .info { text-align: left; line-height: 1.2; }
        .sp-user-btn .info strong { display: block; font-size: .82rem; color: var(--ink); font-weight: 700; }
        .sp-user-btn .info span { font-size: .68rem; color: var(--ink-muted); font-weight: 500; }

        .sp-content { padding: 28px 32px 56px; flex: 1; }

        /* ---------- NProgress ---------- */
        #nprogress .bar { background: linear-gradient(90deg, #7C3AED, #C88719) !important; height: 3px !important; }
        #nprogress .peg { box-shadow: 0 0 10px #7C3AED, 0 0 5px #C88719 !important; }
        #nprogress .spinner { display: none; }

        /* ---------- Backdrop (mobile) ---------- */
        .sp-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(24,7,51,.4);
            z-index: 1035;
        }

        @media (max-width: 991.98px) {
            .sp-sidebar { transform: translateX(-100%); }
            .sp-sidebar.show { transform: translateX(0); }
            .sp-main { margin-left: 0; }
            .sp-toggle { display: inline-flex; }
            .sp-backdrop.show { display: block; }
        }
    </style>
</head>
<body>

<div class="sp-backdrop" id="spBackdrop"></div>

<aside class="sp-sidebar" id="spSidebar">
    <div class="sp-sidebar__brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SKM" onerror="this.style.display='none'">
        <div>
            <div class="name">SKM</div>
            <div class="role">{{ auth()->user()->puskesmas->nama ?? 'Puskesmas' }}</div>
        </div>
    </div>

    <nav class="sp-nav">
        <a href="{{ route('puskesmas.dashboard') }}" class="{{ request()->routeIs('puskesmas.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-home"></i> Dashboard
        </a>

        @role('admin-puskesmas')
        <div class="sp-label">Pengaturan</div>
        <a href="{{ route('puskesmas.pertanyaan.index') }}" class="{{ request()->routeIs('puskesmas.pertanyaan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines"></i> Pertanyaan Survei
        </a>
        <a href="{{ route('puskesmas.unit-layanan.index') }}" class="{{ request()->routeIs('puskesmas.unit-layanan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-hospital"></i> Unit Layanan
        </a>
        @endrole

        <div class="sp-label">Pemantauan</div>
        <a href="{{ route('puskesmas.laporan.index') }}" class="{{ request()->routeIs('puskesmas.laporan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Laporan
        </a>
        <a href="{{ route('puskesmas.tindak-lanjut.index') }}" class="{{ request()->routeIs('puskesmas.tindak-lanjut.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-check"></i> Tindak Lanjut
        </a>

        <div class="sp-label">Lainnya</div>
        <a href="{{ route('survei.create', auth()->user()->puskesmas) }}" target="_blank">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Link Survei
        </a>

        @role('dinkes')
        <a href="{{ route('dinkes.dashboard') }}" class="sp-switch">
            <i class="fa-solid fa-building-columns"></i> Panel Pengawasan Dinkes
        </a>
        @endrole
    </nav>

    <div class="sp-sidebar__foot">
        &copy; {{ date('Y') }} SKM &middot; {{ auth()->user()->puskesmas->nama ?? 'Puskesmas' }}
    </div>
</aside>

<div class="sp-main">
    <header class="sp-header">
        <div class="d-flex align-items-center gap-3">
            <button class="sp-toggle" id="spToggle" type="button" aria-label="Buka menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h1>@yield('title', 'Dashboard')</h1>
        </div>

        <div class="dropdown">
            <button class="sp-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                <span class="info">
                    <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
                    <span>{{ auth()->user()->puskesmas->nama ?? 'Puskesmas' }}</span>
                </span>
                <i class="fa-solid fa-chevron-down text-muted small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">
                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fa-solid fa-user-gear me-2" style="color: var(--sp-purple-700);"></i> Profil
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fa-solid fa-right-from-bracket me-2 text-danger"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <main class="sp-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@include('partials.tom-select')
<script>
    const spSidebar = document.getElementById('spSidebar');
    const spToggle = document.getElementById('spToggle');
    const spBackdrop = document.getElementById('spBackdrop');

    function spCloseSidebar() {
        spSidebar.classList.remove('show');
        spBackdrop.classList.remove('show');
    }

    spToggle?.addEventListener('click', () => {
        spSidebar.classList.toggle('show');
        spBackdrop.classList.toggle('show');
    });
    spBackdrop?.addEventListener('click', spCloseSidebar);
</script>

<!-- NProgress -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<script>
    NProgress.configure({ showSpinner: false, trickleSpeed: 120, minimum: 0.15 });
    NProgress.start();
    window.addEventListener('load', () => NProgress.done());

    document.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (!link) return;
        const url = link.getAttribute('href') || '';
        const isSameOrigin = link.hostname === window.location.hostname;
        const isHash = url.startsWith('#');
        const isNewTab = link.target === '_blank';
        const isSpecial = url.startsWith('mailto:') || url.startsWith('tel:') || url.startsWith('javascript:');
        if (isSameOrigin && !isHash && !isNewTab && !isSpecial) NProgress.start();
    });

    document.addEventListener('submit', function () { NProgress.start(); });
</script>

@include('partials.sp-puskesmas-css')
@include('partials.skrip-salin-tabel')
</body>
</html>