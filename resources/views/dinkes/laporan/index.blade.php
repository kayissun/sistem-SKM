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
        <div class="d-flex justify-content-end mb-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="salinTabelKeClipboard('tabel-rekap-gabungan', this)">
                Salin Tabel
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table id="tabel-rekap-gabungan" class="table table-bordered bg-white" style="min-width: 1200px;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">NO</th>
                        <th>OPD/Unit Pelayanan Publik</th>
                        <th class="text-center">Periode Pelaksanaan</th>
                        <th colspan="9" class="text-center">Nilai Per Unsur</th>
                        <th class="text-center">IKM</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Jumlah Responden</th>
                        <th class="text-center">Metode SKM</th>
                        <th class="text-center">Unsur Prioritas Perbaikan</th>
                        <th class="text-center">Rencana Tindak Lanjut</th>
                        <th class="text-center" style="width: 80px;">Detail</th>
                    </tr>
                    <tr>
                        <th colspan="3"></th>
                        <th class="text-center" style="width: 60px;">U1</th>
                        <th class="text-center" style="width: 60px;">U2</th>
                        <th class="text-center" style="width: 60px;">U3</th>
                        <th class="text-center" style="width: 60px;">U4</th>
                        <th class="text-center" style="width: 60px;">U5</th>
                        <th class="text-center" style="width: 60px;">U6</th>
                        <th class="text-center" style="width: 60px;">U7</th>
                        <th class="text-center" style="width: 60px;">U8</th>
                        <th class="text-center" style="width: 60px;">U9</th>
                        <th colspan="7"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekap as $index => $baris)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $baris['puskesmas'] }}</td>
                            <td class="text-center">{{ $periode->nama }}</td>
                            @foreach (['U1', 'U2', 'U3', 'U4', 'U5', 'U6', 'U7', 'U8', 'U9'] as $unsur)
                                <td class="text-center">
                                    {{ isset($baris['per_unsur'][$unsur]) ? number_format($baris['per_unsur'][$unsur]['nrr_skala_100'], 2, ',', '.') : '-' }}
                                </td>
                            @endforeach
                            <td class="text-center">{{ $baris['nilai_akhir_skm'] }}</td>
                            <td class="text-center">{{ $baris['mutu_akhir'] }}</td>
                            <td class="text-center">{{ $baris['jumlah_responden'] }}</td>
                            <td class="text-center">SKM Online</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">
                                <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $baris['puskesmas_id'], 'periode_survei_id' => $periode->id]) }}"
                                   class="btn btn-sm btn-outline-primary">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="22" class="text-center text-muted">Belum ada unit aktif</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection
