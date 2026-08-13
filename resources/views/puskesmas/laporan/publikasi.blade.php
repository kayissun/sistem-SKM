@extends('layouts.puskesmas')

@section('title', 'Format Publikasi IKM')

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3 no-print">
        <h3 class="mb-0">Format Publikasi IKM</h3>
        <a href="{{ route('puskesmas.laporan.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Laporan</a>
    </div>

    <form method="GET" class="row g-2 mb-3 no-print" style="max-width:500px">
        <div class="col-5">
            <select name="periode_survei_id" class="form-select" onchange="this.form.submit()">
                @foreach ($daftarPeriode as $p)
                    <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                        {{ $p->nama }} @if($p->is_active) (aktif) @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-7">
            <select name="unit_layanan_id" class="form-select" onchange="this.form.submit()">
                <option value="">Seluruh Layanan</option>
                @foreach ($daftarUnitLayanan as $unit)
                    <option value="{{ $unit->id }}" @selected($unitLayanan && $unitLayanan->id === $unit->id)>
                        {{ $unit->nama }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if (!$periode || !$publikasi)
        <div class="alert alert-warning no-print">Belum ada periode survei yang dipilih atau tersedia.</div>
    @else
        <div class="mb-3 no-print">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">Cetak / Print</button>
            <a href="{{ route('puskesmas.laporan.publikasi.export-pdf', ['periode_survei_id' => $periode->id, 'unit_layanan_id' => $unitLayanan->id ?? '']) }}" class="btn btn-outline-danger btn-sm">
                Download PDF
            </a>
        </div>

        @include('partials.publikasi-ikm', [
            'namaOrganisasi' => config('organisasi.nama'),
            'namaUnit' => $puskesmas->nama,
            'namaLayanan' => $unitLayanan->nama ?? $puskesmas->nama,
            'periode' => $periode,
            'publikasi' => $publikasi,
        ])
    @endif

    <style>
        @media print {
            .no-print, .navbar, footer { display: none !important; }
            body { background: white !important; }
        }
    </style>
@endsection
