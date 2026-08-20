@extends('layouts.dinkes')

@section('title', 'Format Publikasi IKM')

@section('content')

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 no-print">
        <div>
            <h3 class="fw-bold mb-0" style="color:#180733">Format Publikasi IKM</h3>
            <p class="text-muted small mb-0">{{ $puskesmas->nama }} &middot; Periode {{ $periode->nama }}</p>
        </div>
        <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-light border text-secondary rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Detail Laporan
        </a>
    </div>

    <!-- Filter Layanan & Tombol Cetak -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 no-print" style="max-width: 600px;">
        <div class="row g-2 align-items-center">
            <div class="col-md-7">
                <form method="GET">
                    <label class="form-label small text-muted mb-1 fw-semibold">Pilih Unit Layanan</label>
                    <select name="unit_layanan_id" class="form-select border rounded-3" onchange="this.form.submit()">
                        <option value="">Seluruh Layanan</option>
                        @foreach ($daftarUnitLayanan as $unit)
                            <option value="{{ $unit->id }}" @selected($unitLayanan && $unitLayanan->id === $unit->id)>
                                {{ $unit->nama }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="col-md-5 d-flex gap-2 align-self-end">
                <button type="button" class="btn btn-primary w-100 fw-medium rounded-3" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Cetak
                </button>
                <a href="{{ route('dinkes.laporan.publikasi.export-pdf', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id, 'unit_layanan_id' => $unitLayanan->id ?? '']) }}" class="btn btn-outline-danger w-100 fw-medium rounded-3">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        @include('partials.publikasi-ikm', [
            'namaOrganisasi' => config('organisasi.nama'),
            'namaUnit' => $puskesmas->nama,
            'namaLayanan' => $unitLayanan->nama ?? $puskesmas->nama,
            'periode' => $periode,
            'publikasi' => $publikasi,
        ])
    </div>

    <style>
        @media print {
            .no-print, .navbar, footer { display: none !important; }
            body { background: white !important; }
            .card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
    </style>
@endsection