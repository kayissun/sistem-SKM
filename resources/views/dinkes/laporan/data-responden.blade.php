@extends('layouts.dinkes')

@section('title', 'Data Responden')

@section('content')
    <div class="mb-3">
        <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-light border text-secondary rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Detail Laporan
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-0" style="color:#180733">Data Responden</h3>
            <p class="text-muted small mb-0">{{ $puskesmas->nama }} &middot; Periode {{ $periode->nama }}</p>
        </div>
        <a href="{{ route('dinkes.laporan.data-responden.export-excel', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm rounded-3 fw-medium">
            <i class="fa-solid fa-file-excel me-1"></i> Export Excel (Semua Responden)
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-3">
        @include('partials.data-responden', ['judul' => $puskesmas->nama, 'id' => 'tabel-data-responden'])
    </div>
@endsection