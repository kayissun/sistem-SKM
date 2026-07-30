@extends('layouts.dinkes')

@section('title', 'Dashboard')

@section('content')
    <h3 class="mb-4">Dashboard Dinas Kesehatan</h3>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Unit aktif</div>
                    <div class="fs-2 fw-bold">{{ $jumlahUnit }}</div>
                    <a href="{{ route('dinkes.puskesmas.index') }}" class="small">Kelola unit &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Periode survei aktif</div>
                    <div class="fs-4 fw-bold">{{ $periodeAktif->nama ?? 'Belum ada' }}</div>
                    <a href="{{ route('dinkes.periode-survei.index') }}" class="small">Kelola periode &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Laporan</div>
                    <div class="fs-6">Lihat rekap IKM seluruh unit</div>
                    <a href="{{ route('dinkes.laporan.index') }}" class="small">Buka laporan &rarr;</a>
                </div>
            </div>
        </div>
    </div>
@endsection
