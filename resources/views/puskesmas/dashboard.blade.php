@extends('layouts.puskesmas')

@section('title', 'Dashboard')

@section('content')

<style>
    /* ===== Page header (style dipindah dari Dashboard Dinkes) ===== */
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

    /* ===== Stat card ===== */
    .sp-stat-card {
        background: #fff;
        border: 1px solid rgba(24,7,51,.06);
        border-radius: 14px;
        transition: box-shadow .18s, transform .18s;
    }
    .sp-stat-card:hover {
        box-shadow: 0 8px 20px rgba(46,16,101,.07);
        transform: translateY(-1px);
    }
    .sp-stat-card .card-body { padding: 20px 22px; }
    .sp-stat-card .icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem;
        flex-shrink: 0;
    }
    .sp-stat-card .icon.purple { background: var(--purple-100); color: var(--purple-700); }
    .sp-stat-card .icon.gold   { background: var(--gold-100);   color: var(--gold-700); }
    .sp-stat-card .icon.green  { background: #ECFDF5;           color: #059669; }
    .sp-stat-card .label {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--ink-muted);
        margin-bottom: 4px;
    }
    .sp-stat-card .value {
        font-weight: 800;
        color: var(--purple-900);
        line-height: 1.2;
        margin-bottom: 4px;
    }
    .sp-stat-card a {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .78rem;
        font-weight: 700;
        color: var(--purple-700);
        margin-top: 8px;
        text-decoration: none;
        transition: gap .15s;
    }
    .sp-stat-card a:hover { gap: 8px; }
    .sp-stat-card .text-purple {
        display: inline-block;
        margin-top: 4px;
        color: var(--gold-700) !important;
        background: var(--gold-100);
        border-radius: 99px;
        padding: 2px 10px;
        font-weight: 700 !important;
    }

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
</style>

<div class="sp-pagehead">
    <div>
        <div class="eyebrow">{{ $puskesmas->nama }}</div>
        <h1>Dashboard</h1>
    </div>
    <div class="meta">
        <div class="meta-item">
            <i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('l, d F Y') }}
        </div>
        @if ($periodeAktif)
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon gold">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <div class="label">Periode Survei Aktif</div>
                    <div class="value">{{ $periodeAktif->nama ?? 'Belum ada' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon purple">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <div class="label">Responden Periode Ini</div>
                    <div class="value">{{ $hasilPeriodeAktif['jumlah_responden'] ?? 0 }}</div>
                    <a href="{{ route('puskesmas.laporan.index') }}">Lihat laporan</a>
                </div>
            </div>
        </div>
    </div>

    @if ($hasilPeriodeAktif && $hasilPeriodeAktif['jumlah_responden'] > 0)
    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon green">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div>
                    <div class="label">Nilai SKM Periode Ini</div>
                    <div class="value">{{ $hasilPeriodeAktif['nilai_akhir_skm'] }}</div>
                    <span class="small fw-bold text-purple">{{ $hasilPeriodeAktif['mutu_akhir'] }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card sp-section-card h-100">
            <div class="card-header">Akses Cepat</div>
            <div class="card-body">
                <div class="row g-3 sp-quick">
                    @role('admin-puskesmas')
                    <div class="col-md-6">
                        <a href="{{ route('puskesmas.pertanyaan.create') }}">
                            <i class="fa-solid fa-file-lines"></i> Form Builder Pertanyaan
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('puskesmas.unit-layanan.index') }}">
                            <i class="fa-solid fa-hospital"></i> Unit Layanan / Poli
                        </a>
                    </div>
                    @endrole
                    <div class="col-md-6">
                        <a href="{{ route('puskesmas.tindak-lanjut.index') }}">
                            <i class="fa-solid fa-clipboard-check"></i> Tindak Lanjut
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('survei.create', $puskesmas) }}" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Link Survei Publik
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sp-section-card h-100">
            <div class="card-header">QR Code Survei</div>
            <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                <img src="{{ route('qrcode.tampil', $puskesmas) }}" width="200" height="200" alt="QR survei {{ $puskesmas->nama }}" class="rounded border p-1" style="border-color:#F0DFB2 !important;">
                <div class="mt-3 d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('qrcode.unduh', $puskesmas) }}" class="btn btn-gold btn-sm">
                        <i class="fa-solid fa-download me-1"></i> Unduh QR
                    </a>
                    <a href="{{ route('survei.create', $puskesmas) }}" target="_blank" class="btn btn-outline-gold btn-sm">
                        <i class="fa-solid fa-link me-1"></i> Buka Link
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection