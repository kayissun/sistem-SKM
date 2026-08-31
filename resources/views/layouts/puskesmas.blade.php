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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
            --sp-ink-muted:  #635C7A;
            --sp-sidebar-w:  268px;
            --bs-primary: #6D28D9;
            --bs-primary-rgb: 109, 40, 217;
            --bs-link-color: #6D28D9;
            --bs-link-hover-color: #2E1065;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F4F2FB;
            color: var(--sp-ink);
        }

        a { text-decoration: none; }

        /* ---------- Bootstrap component reskin ---------- */
        .btn { border-radius: 10px; font-weight: 600; font-size: .875rem; }
        .btn-primary {
            background: linear-gradient(135deg, #7C3AED 0%, #2A0B5E 100%);
            border: none;
            box-shadow: 0 8px 16px -6px rgba(46,16,101,.4);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #8B5CF6 0%, #341176 100%);
            box-shadow: 0 10px 18px -6px rgba(46,16,101,.5);
        }
        .btn-outline-primary { color: var(--sp-purple-700); border-color: rgba(109,40,217,.35); }
        .btn-outline-primary:hover { background: var(--sp-purple-100); color: var(--sp-purple-800); border-color: var(--sp-purple-700); }
        .btn-outline-light { border-color: rgba(255,255,255,.35); }

        .card {
            border: 1px solid rgba(109,40,217,.08);
            border-radius: 16px;
            box-shadow: 0 10px 24px -14px rgba(46,16,101,.18);
        }
        .card-header { background: var(--sp-purple-50); border-bottom: 1px solid rgba(109,40,217,.08); font-weight: 700; color: var(--sp-purple-900); border-radius: 16px 16px 0 0 !important; }

        .table thead th {
            background: var(--sp-purple-50);
            color: var(--sp-purple-900);
            font-weight: 700;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: none;
        }
        .table { border-radius: 12px; overflow: hidden; }

        .alert { border-radius: 12px; border-width: 1px; }
        .alert-success { background: #ECFDF5; border-color: #A7F3D0; color: #065F46; }
        .alert-danger  { background: #FEF2F2; border-color: #FECACA; color: #991B1B; }

        .badge.bg-primary { background: var(--sp-purple-700) !important; }

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
            padding: 22px 22px 18px;
        }
        .sp-sidebar__brand img { width: 36px; height: auto; }
        .sp-sidebar__brand .name { color: #fff; font-weight: 800; font-size: 1.02rem; line-height: 1.15; }
        .sp-sidebar__brand .role { color: rgba(255,255,255,.5); font-size: .72rem; font-weight: 600; }

        .sp-nav {
            flex: 1;
            overflow-y: auto;
            padding: 6px 14px 14px;
        }
        .sp-nav .sp-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            margin: 16px 12px 8px;
        }
        .sp-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: rgba(255,255,255,.72);
            font-weight: 600;
            font-size: .875rem;
            margin-bottom: 2px;
            transition: background .15s ease, color .15s ease;
        }
        .sp-nav a i { width: 18px; text-align: center; font-size: .92rem; }
        .sp-nav a:hover { background: rgba(255,255,255,.08); color: #fff; }
        .sp-nav a.active {
            background: rgba(255,255,255,.12);
            color: #fff;
            box-shadow: inset 3px 0 0 var(--sp-gold-400);
        }
        .sp-nav a.sp-switch {
            background: rgba(228,166,59,.14);
            color: #F5D28A;
        }
        .sp-nav a.sp-switch:hover { background: rgba(228,166,59,.24); color: #fff; }

        .sp-sidebar__foot {
            padding: 14px 22px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
            color: rgba(255,255,255,.4);
            font-size: .72rem;
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
            background: #fff;
            border-bottom: 1px solid rgba(109,40,217,.08);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .sp-header h1 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--sp-purple-900);
            margin: 0;
        }

        .sp-toggle {
            display: none;
            border: none;
            background: var(--sp-purple-50);
            color: var(--sp-purple-700);
            width: 38px;
            height: 38px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
        }

        .sp-user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(109,40,217,.15);
            background: #fff;
            border-radius: 99px;
            padding: 6px 14px 6px 6px;
        }
        .sp-user-btn .avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg,#7C3AED,#2A0B5E);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .8rem;
        }
        .sp-user-btn .info { text-align: left; line-height: 1.15; }
        .sp-user-btn .info strong { display: block; font-size: .85rem; color: var(--sp-ink); }
        .sp-user-btn .info span { font-size: .72rem; color: var(--sp-ink-muted); }

        .sp-content { padding: 26px 28px 48px; flex: 1; }

        .sp-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(24,7,51,.45);
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
        <a href="{{ route('puskesmas.petugas.index') }}" class="{{ request()->routeIs('puskesmas.petugas.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i> Petugas
        </a>
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

@include('partials.sp-puskesmas-css')
@include('partials.skrip-salin-tabel')
</body>
</html>
