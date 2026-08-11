@extends('layouts.dinkes')

@section('title', 'Data Responden')

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <h3 class="mb-0">Data Responden — {{ $puskesmas->nama }}</h3>
        <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-outline-secondary">
            &larr; Kembali ke Laporan
        </a>
    </div>

    <div class="mb-3">
        <a href="{{ route('dinkes.laporan.data-responden.export-excel', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm">
            Export Excel (semua responden)
        </a>
    </div>

    @include('partials.data-responden', ['judul' => $puskesmas->nama, 'id' => 'tabel-data-responden'])
@endsection
