@extends('layouts.dinkes')
@section('title', 'Dashboard')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">

<style>
    :root {
        --surface-0: #FFFFFF;
        --surface-1: #FAF8FF;
        --surface-2: #F3EEFF;
        --purple-900: #180733;
        --purple-800: #2E1065;
        --purple-700: #6D28D9;
        --purple-600: #7C3AED;
        --purple-500: #8B5CF6;
        --purple-100: #EDE9FE;
        --ink: #14102B;
        --ink-muted: #625B78;
        --gold-700: #A66A0E;
        --gold-600: #C88719;
        --gold-400: #E4A63B;
        --gold-100: #FCF1DC;
        --gradient-primary: linear-gradient(135deg, #7C3AED 0%, #5B21B6 55%, #2A0B5E 100%);
    }

    /* ===== Page header: fungsional, tanpa sapaan personal ===== */
    .sp-pagehead {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid rgba(24, 7, 51, .08);
    }
    .sp-pagehead .eyebrow {
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--gold-700);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sp-pagehead .eyebrow::before {
        content: '';
        width: 18px;
        height: 3px;
        border-radius: 2px;
        background: var(--gradient-primary);
        display: inline-block;
    }
    .sp-pagehead h1 {
        font-weight: 800;
        font-size: 1.5rem;
        color: var(--purple-900);
        margin: 0;
        letter-spacing: -.01em;
    }
    .sp-pagehead .meta {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }
    .sp-pagehead .meta-item {
        font-size: .82rem;
        color: var(--ink-muted);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .sp-pagehead .meta-item i { color: var(--purple-600); }
    .sp-pagehead .meta-item.status-ok i { color: #10B981; }

    /* ===== Stat cards ===== */
    .sp-stat-card {
        border: 1px solid rgba(24, 7, 51, .06);
        border-radius: 16px;
        transition: box-shadow .18s, transform .18s;
    }
    .sp-stat-card:hover {
        box-shadow: 0 10px 26px rgba(46, 16, 101, .10);
        transform: translateY(-2px);
    }
    .sp-stat-card .icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.05rem;
        margin-bottom: 14px;
    }
    .sp-stat-card .label {
        font-size: .78rem;
        color: var(--ink-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .sp-stat-card .value { font-weight: 800; color: var(--purple-900); margin: 4px 0 10px; }
    .sp-stat-card a {
        font-size: .83rem; font-weight: 700; color: var(--purple-700);
        text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .sp-stat-card a:hover { color: var(--purple-800); gap: 8px; }
    .sp-stat-card a i { transition: margin .15s; }

    /* ===== Akses cepat ===== */
    .sp-section-card {
        border: 1px solid rgba(24, 7, 51, .06);
        border-radius: 16px;
        overflow: hidden;
    }
    .sp-section-card .card-header {
        background: var(--surface-1);
        border-bottom: 1px solid rgba(24, 7, 51, .06);
        font-weight: 800;
        color: var(--purple-900);
        font-size: .95rem;
        padding: 14px 20px;
    }
    .sp-quick a {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid rgba(109, 40, 217, .10);
        background: #fff;
        font-weight: 600; font-size: .88rem; color: var(--ink);
        text-decoration: none;
        transition: .15s;
    }
    .sp-quick a:hover { background: var(--surface-1); border-color: rgba(109, 40, 217, .25); }
    .sp-quick a i {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: var(--purple-100); color: var(--purple-700);
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; flex-shrink: 0;
    }

    /* ===== Tabel klaster performa ===== */
    .sp-cluster-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .88rem;
    }
    .sp-cluster-table thead th {
        text-align: left;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--ink-muted);
        background: var(--surface-1);
        padding: 12px 16px;
        border-bottom: 1px solid rgba(24, 7, 51, .08);
    }
    .sp-cluster-table thead th:first-child { border-top-left-radius: 10px; }
    .sp-cluster-table thead th:last-child { border-top-right-radius: 10px; }
    .sp-cluster-table tbody td {
        padding: 12px 16px;
        color: var(--ink);
        border-bottom: 1px solid rgba(24, 7, 51, .05);
    }
    .sp-cluster-table tbody tr:last-child td { border-bottom: none; }
    .sp-cluster-table tbody tr:hover td { background: var(--surface-1); }
    .sp-cluster-table .unit-name { font-weight: 700; color: var(--purple-900); }
    .sp-cluster-table .nilai { font-weight: 800; color: var(--purple-700); }
    .sp-badge-cluster {
        display: inline-block;
        padding: 3px 11px;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 700;
    }
    .sp-badge-cluster.c-tinggi { background: #DCFCE7; color: #15803D; }
    .sp-badge-cluster.c-sedang { background: var(--gold-100); color: var(--gold-700); }
    .sp-badge-cluster.c-rendah { background: #FEE2E2; color: #B91C1C; }

    .sp-empty-state {
        text-align: center;
        padding: 46px 20px;
        color: var(--ink-muted);
    }
    .sp-empty-state i {
        font-size: 1.8rem;
        color: var(--purple-500);
        margin-bottom: 10px;
        display: block;
    }

    /* loadingbar */
    #nprogress .bar {
        background: linear-gradient(90deg, #7C3AED, #C88719) !important;
        height: 3px !important;
    }
    #nprogress .peg {
        box-shadow: 0 0 10px #7C3AED, 0 0 5px #C88719 !important;
    }
    #nprogress .spinner { display: none; }
</style>

<div class="sp-pagehead">
    <div>
        <div class="eyebrow">Dinkesda Kabupaten Purworejo</div>
        <h1>Dashboard</h1>
    </div>
    <div class="meta">
        <div class="meta-item">
            <i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('l, d F Y') }}
        </div>
        <div class="meta-item status-ok">
            <i class="fa-solid fa-circle-check"></i> {{ $periodeAktif->nama ?? 'Belum ada periode aktif' }}
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body">
                <div class="icon" style="background: linear-gradient(135deg,#7C3AED,#2A0B5E)">
                    <i class="fa-solid fa-hospital"></i>
                </div>
                <div class="label">Unit Aktif</div>
                <div class="value fs-2">{{ $jumlahUnit }}</div>
                <a href="{{ route('dinkes.puskesmas.index') }}">Kelola unit <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body">
                <div class="icon" style="background: linear-gradient(135deg,#C88719,#E4A63B)">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="label">Periode Survei Aktif</div>
                <div class="value fs-5">{{ $periodeAktif->nama ?? 'Belum ada' }}</div>
                <a href="{{ route('dinkes.periode-survei.index') }}">Kelola periode <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body">
                <div class="icon" style="background: linear-gradient(135deg,#10B981,#047857)">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="label">Laporan</div>
                <div class="value fs-6">Rekap IKM seluruh unit</div>
                <a href="{{ route('dinkes.laporan.index') }}">Buka laporan <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="card sp-section-card mt-4">
    <div class="card-header">Akses Cepat</div>
    <div class="card-body">
        <div class="row g-3 sp-quick">
            <div class="col-md-4">
                <a href="{{ route('dinkes.unsur-pelayanan.index') }}">
                    <i class="fa-solid fa-list-check"></i> Unsur Pelayanan
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('dinkes.klaster.index') }}">
                    <i class="fa-solid fa-layer-group"></i> Klaster Performa
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('dinkes.aktivitas.index') }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Log Aktivitas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card sp-section-card mt-4">
    <div class="card-header">Klaster Performa Unit</div>
    <div class="card-body p-0">
        @if($clusters->count())
        <div class="table-responsive">
            <table class="sp-cluster-table">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Nilai</th>
                        <th>Cluster</th>
                        <th>Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clusters as $item)
                    @php
                        $clusterLabel = $item->cluster_nama ?: $item->cluster;
                        $clusterKey = strtolower((string) $clusterLabel);
                        $badgeClass = str_contains($clusterKey, 'tinggi') || str_contains($clusterKey, 'baik')
                            ? 'c-tinggi'
                            : (str_contains($clusterKey, 'rendah') || str_contains($clusterKey, 'kurang')
                                ? 'c-rendah'
                                : 'c-sedang');
                    @endphp
                    <tr>
                        <td class="unit-name">{{ $item->puskesmas->nama ?? '-' }}</td>
                        <td class="nilai">{{ $item->nilai_rata2 }}</td>
                        <td><span class="sp-badge-cluster {{ $badgeClass }}">{{ $clusterLabel }}</span></td>
                        <td>{{ $item->rekomendasi }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="sp-empty-state">
            <i class="fa-solid fa-chart-simple"></i>
            Belum ada data clustering untuk periode ini.
        </div>
        @endif
    </div>
</div>

<!-- loading-bar -->
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

        if (isSameOrigin && !isHash && !isNewTab && !isSpecial) {
            NProgress.start();
        }
    });

    document.addEventListener('submit', function () {
        NProgress.start();
    });
</script>

@endsection