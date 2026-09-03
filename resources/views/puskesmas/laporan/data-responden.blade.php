@extends('layouts.puskesmas')

@section('title', 'Data Responden')

<style>
    .sp-back-link {
            color: var(--ink-muted);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .sp-back-link:hover {
            color: var(--purple-700);
        }
</style>

@section('content')
    <div class="mb-3">
        <a href="{{ route('puskesmas.laporan.index') }}" class="sp-back-link">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Laporan
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-0" style="color:#180733">Data Responden</h3>
            <p class="text-muted small mb-0">Daftar responden yang mengisi survei pada periode ini.</p>
        </div>
        @if ($periode)
            <a href="{{ route('puskesmas.laporan.data-responden.export-excel', ['periode_survei_id' => $periode->id]) }}" class="sp-btn-excel">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        @endif
    </div>

    <!-- Filter Periode -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="max-width:420px;">
        <form method="GET">
            <label class="form-label small text-muted mb-1 fw-semibold">
                <i class="fa-solid fa-calendar-days me-1"></i> Periode Survei
            </label>
            <select name="periode_survei_id" class="form-select border rounded-3" onchange="this.form.submit()">
                @foreach ($daftarPeriode as $p)
                    <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                        {{ $p->nama }} @if($p->is_active) (Aktif) @endif
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if (!$periode || !$hasil)
        <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 text-center">
            <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 text-warning"></i>
            <h6 class="fw-bold mb-1">Belum Ada Data</h6>
            <p class="mb-0 small text-muted">Pilih periode survei untuk melihat data responden.</p>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-3">
            @include('partials.data-responden', ['judul' => $puskesmas->nama, 'id' => 'tabel-data-responden'])
        </div>
    @endif
@endsection
