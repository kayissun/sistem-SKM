@extends('layouts.puskesmas')

@section('title', 'Data Responden')

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <h3 class="mb-0">Data Responden</h3>
        <a href="{{ route('puskesmas.laporan.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Laporan</a>
    </div>

    <form method="GET" class="row g-2 mb-3" style="max-width:400px">
        <div class="col-8">
            <select name="periode_survei_id" class="form-select" onchange="this.form.submit()">
                @foreach ($daftarPeriode as $p)
                    <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                        {{ $p->nama }} @if($p->is_active) (aktif) @endif
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if (!$periode || !$hasil)
        <div class="alert alert-warning">Belum ada periode survei yang dipilih atau tersedia.</div>
    @else
        <div class="mb-3">
            <a href="{{ route('puskesmas.laporan.data-responden.export-excel', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm">
                Export Excel (semua responden)
            </a>
        </div>

        @include('partials.data-responden', ['judul' => $puskesmas->nama, 'id' => 'tabel-data-responden'])
    @endif
@endsection
