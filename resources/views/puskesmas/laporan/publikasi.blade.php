@extends('layouts.puskesmas')

@section('title', 'Format Publikasi IKM')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 no-print">
        <div>
            <h3 class="fw-bold mb-0" style="color:#180733">Format Publikasi IKM</h3>
            <p class="text-muted small mb-0">Rekap hasil survei kepuasan masyarakat untuk dipublikasikan.</p>
        </div>
        <a href="{{ route('puskesmas.laporan.index') }}" class="btn btn-sm btn-light border text-secondary rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Laporan
        </a>
    </div>

    <!-- Filter & Tombol Cetak -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 no-print" style="max-width:600px;">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1 fw-semibold">
                    <i class="fa-solid fa-calendar-days me-1"></i> Periode Survei
                </label>
                <select name="periode_survei_id" class="form-select border rounded-3" onchange="window.location='{{ route('puskesmas.laporan.publikasi') }}?periode_survei_id='+this.value+'&unit_layanan_id='+(document.querySelector('[name=unit_layanan_id]')?.value||'')">
                    @foreach ($daftarPeriode as $p)
                        <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                            {{ $p->nama }} @if($p->is_active) (Aktif) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1 fw-semibold">
                    <i class="fa-solid fa-building me-1"></i> Unit Layanan
                </label>
                <select name="unit_layanan_id" class="form-select border rounded-3" onchange="window.location='{{ route('puskesmas.laporan.publikasi') }}?periode_survei_id='+(document.querySelector('[name=periode_survei_id]')?.value||'')+'&unit_layanan_id='+this.value">
                    <option value="">Seluruh Layanan</option>
                    @foreach ($daftarUnitLayanan as $unit)
                        <option value="{{ $unit->id }}" @selected($unitLayanan && $unitLayanan->id === $unit->id)>
                            {{ $unit->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-self-end">
                <button type="button" class="btn btn-primary w-100 fw-medium rounded-3" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Cetak
                </button>
                <a href="{{ route('puskesmas.laporan.publikasi.export-pdf', ['periode_survei_id' => $periode->id, 'unit_layanan_id' => $unitLayanan->id ?? '']) }}" class="btn btn-outline-danger w-100 fw-medium rounded-3">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>

    @if (!$periode || !$publikasi)
        <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 text-center no-print">
            <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 text-warning"></i>
            <h6 class="fw-bold mb-1">Belum Ada Data Publikasi</h6>
            <p class="mb-0 small text-muted">Pilih periode survei untuk melihat format publikasi IKM.</p>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-4">
            @include('partials.publikasi-ikm', [
                'namaOrganisasi' => config('organisasi.nama'),
                'namaUnit' => $puskesmas->nama,
                'namaLayanan' => $unitLayanan->nama ?? $puskesmas->nama,
                'periode' => $periode,
                'publikasi' => $publikasi,
            ])
        </div>
    @endif

    <style>
        @media print {
            .no-print, .sp-sidebar, .sp-header-top { display: none !important; }
            .sp-content { margin: 0 !important; padding: 0 !important; }
            body { background: white !important; }
            .card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
    </style>
@endsection
