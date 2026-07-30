@extends('layouts.dinkes')

@section('title', 'Detail Laporan')

@section('content')
    <a href="{{ route('dinkes.laporan.index', ['periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali</a>

    <h3 class="mb-1">{{ $puskesmas->nama }}</h3>
    <p class="text-muted">Periode: {{ $periode->nama }} &middot; Responden: {{ $hasil['jumlah_responden'] }}</p>

    <div class="mb-3">
        <a href="{{ route('dinkes.laporan.detail.export-pdf', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm">Export PDF</a>
        <a href="{{ route('dinkes.laporan.detail.export-excel', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm">Export Excel</a>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Unsur pelayanan</th>
                <th>Total nilai</th>
                <th>NRR</th>
                <th>NRR skala 100</th>
                <th>Kategori</th>
                <th>NRR tertimbang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hasil['per_unsur'] as $kode => $u)
                <tr>
                    <td>{{ $kode }}</td>
                    <td>{{ $u['pertanyaan'] }}</td>
                    <td>{{ $u['total_nilai'] }}</td>
                    <td>{{ $u['nrr'] }}</td>
                    <td>{{ $u['nrr_skala_100'] }}</td>
                    <td>{{ $u['kategori'] }}</td>
                    <td>{{ $u['nrr_tertimbang'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="card d-inline-block">
        <div class="card-body">
            <div class="text-muted small">Nilai akhir SKM</div>
            <div class="fs-3 fw-bold">{{ $hasil['nilai_akhir_skm'] }}</div>
            <div>{{ $hasil['mutu_akhir'] }}</div>
        </div>
    </div>
@endsection
