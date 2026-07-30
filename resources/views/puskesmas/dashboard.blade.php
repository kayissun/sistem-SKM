@extends('layouts.puskesmas')

@section('title', 'Dashboard')

@section('content')
    <h3 class="mb-4">Dashboard {{ $puskesmas->nama }}</h3>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Jumlah petugas</div>
                    <div class="fs-2 fw-bold">{{ $jumlahPetugas }}</div>
                    <a href="{{ route('puskesmas.petugas.index') }}" class="small">Kelola petugas &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Periode aktif</div>
                    <div class="fs-5 fw-bold">{{ $periodeAktif->nama ?? 'Belum ada' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Responden periode ini</div>
                    <div class="fs-2 fw-bold">{{ $hasilPeriodeAktif['jumlah_responden'] ?? 0 }}</div>
                    <a href="{{ route('puskesmas.laporan.index') }}" class="small">Lihat laporan &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    @if ($hasilPeriodeAktif && $hasilPeriodeAktif['jumlah_responden'] > 0)
        <div class="card d-inline-block">
            <div class="card-body">
                <div class="text-muted small">Nilai akhir SKM periode ini</div>
                <div class="fs-3 fw-bold">{{ $hasilPeriodeAktif['nilai_akhir_skm'] }}</div>
                <div>{{ $hasilPeriodeAktif['mutu_akhir'] }}</div>
            </div>
        </div>
    @endif

    <div class="card mt-4" style="max-width:300px">
        <div class="card-body text-center">
            <div class="text-muted small mb-2">QR code survei unit ini</div>
            <img src="{{ route('qrcode.tampil', $puskesmas) }}" width="220" height="220" alt="QR survei {{ $puskesmas->nama }}">
            <div class="mt-3">
                <a href="{{ route('qrcode.unduh', $puskesmas) }}" class="btn btn-outline-primary btn-sm">Unduh QR</a>
                <a href="{{ route('survei.create', $puskesmas) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Buka link</a>
            </div>
        </div>
    </div>
@endsection
