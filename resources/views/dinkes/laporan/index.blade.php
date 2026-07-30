@extends('layouts.dinkes')

@section('title', 'Laporan Rekap')

@section('content')
    <h3 class="mb-3">Laporan Rekap Semua Unit</h3>

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

    @if ($periode)
        <div class="mb-3">
            <a href="{{ route('dinkes.laporan.export-pdf', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm">Export PDF</a>
            <a href="{{ route('dinkes.laporan.export-excel', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm">Export Excel</a>
        </div>
    @endif

    @if (!$periode)
        <div class="alert alert-warning">Belum ada periode survei. Buat periode terlebih dahulu.</div>
    @else
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Jumlah responden</th>
                    <th>Nilai akhir SKM</th>
                    <th>Mutu</th>
                    <th style="width:120px">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap as $baris)
                    <tr>
                        <td>{{ $baris['puskesmas'] }}</td>
                        <td>{{ $baris['jumlah_responden'] }}</td>
                        <td>{{ $baris['nilai_akhir_skm'] }}</td>
                        <td>{{ $baris['mutu_akhir'] }}</td>
                        <td>
                            <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $baris['puskesmas_id'], 'periode_survei_id' => $periode->id]) }}"
                               class="btn btn-sm btn-outline-primary">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Belum ada unit aktif</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
@endsection
