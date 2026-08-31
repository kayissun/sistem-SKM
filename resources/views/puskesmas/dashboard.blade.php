@extends('layouts.puskesmas')

@section('title', 'Dashboard')

@section('content')

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
        <div class="meta-item" style="color:#10B981">
            <i class="fa-solid fa-circle-check"></i> {{ $periodeAktif->nama }}
        </div>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body">
                <div class="icon" style="background: linear-gradient(135deg,#C88719,#E4A63B)">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="label">Periode Survei Aktif</div>
                <div class="value fs-5">{{ $periodeAktif->nama ?? 'Belum ada' }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body">
                <div class="icon" style="background: linear-gradient(135deg,#10B981,#047857)">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="label">Responden Periode Ini</div>
                <div class="value fs-2">{{ $hasilPeriodeAktif['jumlah_responden'] ?? 0 }}</div>
                <a href="{{ route('puskesmas.laporan.index') }}">Lihat laporan <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

@if ($hasilPeriodeAktif && $hasilPeriodeAktif['jumlah_responden'] > 0)
<div class="row g-3 mt-1">
    <div class="col-md-4">
        <div class="card h-100 sp-stat-card">
            <div class="card-body">
                <div class="icon" style="background: linear-gradient(135deg,#3B82F6,#1D4ED8)">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="label">Nilai SKM Periode Ini</div>
                <div class="value fs-2">{{ $hasilPeriodeAktif['nilai_akhir_skm'] }}</div>
                <span class="small fw-bold" style="color:#6D28D9">{{ $hasilPeriodeAktif['mutu_akhir'] }}</span>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card sp-section-card mt-4">
    <div class="card-header">Akses Cepat</div>
    <div class="card-body">
        <div class="row g-3 sp-quick">
            @role('admin-puskesmas')
            <div class="col-md-4">
                <a href="{{ route('puskesmas.pertanyaan.create') }}">
                    <i class="fa-solid fa-file-lines"></i> Form Builder Pertanyaan
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('puskesmas.unit-layanan.index') }}">
                    <i class="fa-solid fa-hospital"></i> Unit Layanan / Poli
                </a>
            </div>
            @endrole
            <div class="col-md-4">
                <a href="{{ route('puskesmas.tindak-lanjut.index') }}">
                    <i class="fa-solid fa-clipboard-check"></i> Tindak Lanjut
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('survei.create', $puskesmas) }}" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Link Survei Publik
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card sp-section-card mt-4" style="max-width:340px">
    <div class="card-body text-center">
        <div class="small text-muted mb-2 fw-semibold">QR Code Survei</div>
        <img src="{{ route('qrcode.tampil', $puskesmas) }}" width="220" height="220" alt="QR survei {{ $puskesmas->nama }}" class="rounded border p-1">
        <div class="mt-3 d-flex gap-2 justify-content-center">
            <a href="{{ route('qrcode.unduh', $puskesmas) }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-download me-1"></i> Unduh QR
            </a>
            <a href="{{ route('survei.create', $puskesmas) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-link me-1"></i> Buka Link
            </a>
        </div>
    </div>
</div>

@endsection
