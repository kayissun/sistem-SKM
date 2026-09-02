@extends('layouts.dinkes')
@section('title', 'Dashboard')
@section('content')

<style>
    /* ===== Page header ===== */
    .sp-pagehead {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 28px;
        padding-bottom: 18px;
        border-bottom: 1px solid rgba(24,7,51,.06);
    }
    .sp-pagehead .eyebrow {
        font-size: .7rem;
        font-weight: 700;
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
        font-size: 1.4rem;
        color: var(--purple-900);
        margin: 0;
        letter-spacing: -.01em;
    }
    .sp-pagehead .meta {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .sp-pagehead .meta-item {
        font-size: .78rem;
        color: var(--ink-muted);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .sp-pagehead .meta-item i { color: var(--purple-600); }
    .sp-pagehead .meta-item.status-ok i { color: #10B981; }

    /* ===== Stat row — clean, no colored icon ===== */
    .sp-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .sp-stat {
        background: #fff;
        border: 1px solid rgba(24,7,51,.06);
        border-radius: 14px;
        padding: 20px 22px;
        transition: box-shadow .18s, transform .18s;
    }
    .sp-stat:hover {
        box-shadow: 0 8px 20px rgba(46,16,101,.07);
        transform: translateY(-1px);
    }
    .sp-stat-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .sp-stat-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem;
        flex-shrink: 0;
    }
    .sp-stat-icon.purple { background: var(--purple-100); color: var(--purple-700); }
    .sp-stat-icon.gold   { background: var(--gold-100);   color: var(--gold-700); }
    .sp-stat-icon.green  { background: #ECFDF5;           color: #059669; }

    .sp-stat .stat-label {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--ink-muted);
        margin-bottom: 4px;
    }
    .sp-stat .stat-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--purple-900);
        line-height: 1.2;
        margin-bottom: 4px;
    }
    .sp-stat .stat-sub {
        font-size: .78rem;
        color: var(--ink-muted);
        font-weight: 500;
    }
    .sp-stat a {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .78rem;
        font-weight: 700;
        color: var(--purple-700);
        margin-top: 8px;
        transition: gap .15s;
    }
    .sp-stat a:hover { gap: 8px; }

    /* ===== Section card ===== */
    .sp-section-card {
        border: 1px solid rgba(24,7,51,.06);
        border-radius: 14px;
        overflow: hidden;
    }
    .sp-section-card .card-header {
        background: var(--surface-1);
        border-bottom: 1px solid rgba(24,7,51,.06);
        font-weight: 700;
        color: var(--purple-900);
        font-size: .88rem;
        padding: 13px 20px;
    }

    /* ===== Quick links ===== */
    .sp-quick a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 16px;
        border-radius: 12px;
        border: 1px solid rgba(109,40,217,.08);
        background: #fff;
        font-weight: 600;
        font-size: .84rem;
        color: var(--ink);
        text-decoration: none;
        transition: .15s;
    }
    .sp-quick a:hover { background: var(--surface-1); border-color: rgba(109,40,217,.2); }
    .sp-quick a i {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: var(--purple-100);
        color: var(--purple-700);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
    }

    /* ===== Cluster table ===== */
    .sp-cluster-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .84rem;
    }
    .sp-cluster-table thead th {
        text-align: left;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--ink-muted);
        background: var(--surface-1);
        padding: 11px 16px;
        border-bottom: 1px solid rgba(24,7,51,.06);
    }
    .sp-cluster-table thead th:first-child { border-top-left-radius: 10px; }
    .sp-cluster-table thead th:last-child { border-top-right-radius: 10px; }
    .sp-cluster-table tbody td {
        padding: 11px 16px;
        color: var(--ink);
        border-bottom: 1px solid rgba(24,7,51,.04);
    }
    .sp-cluster-table tbody tr:last-child td { border-bottom: none; }
    .sp-cluster-table tbody tr:hover td { background: var(--surface-1); }
    .sp-cluster-table .unit-name { font-weight: 700; color: var(--purple-900); }
    .sp-cluster-table .nilai { font-weight: 700; color: var(--purple-700); }

    .sp-badge-cluster {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 99px;
        font-size: .72rem;
        font-weight: 700;
    }
    .sp-badge-cluster.c-tinggi { background: #DCFCE7; color: #15803D; }
    .sp-badge-cluster.c-sedang { background: var(--gold-100); color: var(--gold-700); }
    .sp-badge-cluster.c-rendah { background: #FEE2E2; color: #B91C1C; }

    .sp-empty-state {
        text-align: center;
        padding: 44px 20px;
        color: var(--ink-muted);
    }
    .sp-empty-state i { font-size: 1.6rem; color: var(--purple-500); margin-bottom: 10px; display: block; }

    @media (max-width: 768px) {
        .sp-stats { grid-template-columns: 1fr; }
    }
</style>

<div class="sp-pagehead">
    <div>
        <h1>Dashboard</h1>
    </div>
    <div class="meta">
        <div class="meta-item">
            <i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
</div>

{{-- Stat tanpa icon berwarna --}}
<div class="sp-stats">
    <div class="sp-stat">
        <div class="sp-stat-head">
            <div class="sp-stat-icon purple"><i class="fa-solid fa-hospital"></i></div>
            <div>
                <div class="stat-label">Unit Aktif</div>
                <div class="stat-value">{{ $jumlahUnit }}</div>
            </div>
        </div>
        <div class="stat-sub">Puskesmas &amp; RSU terdaftar</div>
        <a href="{{ route('dinkes.puskesmas.index') }}">Kelola unit</a>
    </div>

    <div class="sp-stat">
        <div class="sp-stat-head">
            <div class="sp-stat-icon gold"><i class="fa-solid fa-calendar-days"></i></div>
            <div>
                <div class="stat-label">Periode Survei Aktif</div>
                <div class="stat-value" style="font-size:1.15rem;">{{ $periodeAktif->nama ?? '—' }}</div>
            </div>
        </div>
        <div class="stat-sub">{{ $periodeAktif ? $periodeAktif->tanggal_mulai->format('d M Y') . ' – ' . $periodeAktif->tanggal_selesai->format('d M Y') : 'Belum ada periode aktif' }}</div>
        <a href="{{ route('dinkes.periode-survei.index') }}">Kelola periode</a>
    </div>

    <div class="sp-stat">
        <div class="sp-stat-head">
            <div class="sp-stat-icon green"><i class="fa-solid fa-chart-line"></i></div>
            <div>
                <div class="stat-label">Laporan IKM</div>
                <div class="stat-value" style="font-size:1.15rem;">Rekap Seluruh Unit</div>
            </div>
        </div>
        <div class="stat-sub">Indeks Kepuasan Masyarakat per unit</div>
        <a href="{{ route('dinkes.laporan.index') }}">Buka laporan</a>
    </div>
</div>

{{-- Akses Cepat --}}
<div class="card sp-section-card mb-4">
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

{{-- Tabel Klaster --}}
<div class="card sp-section-card">
    <div class="card-header">Klaster Performa Unit</div>
    <div class="card-body p-0">
        @if($clusters->count())
        <div class="table-responsive">
            <table class="sp-cluster-table">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Nilai</th>
                        <th>Klaster</th>
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

@endsection