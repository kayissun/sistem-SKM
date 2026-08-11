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
        @php $kodeUnsur = $rekap->isNotEmpty() ? array_keys($rekap->first()['per_unsur']) : []; @endphp

        <div class="d-flex justify-content-end mb-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="salinTabelKeClipboard('tabel-rekap-gabungan', this)">
                Salin Tabel
            </button>
        </div>

        <div class="table-responsive mb-4">
            <table id="tabel-rekap-gabungan" class="table table-bordered text-center align-middle bg-white" style="font-size:0.85rem">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle text-start">OPD/Unit Pelayanan Publik</th>
                        <th rowspan="2" class="align-middle">Periode Pelaksanaan</th>
                        <th colspan="{{ count($kodeUnsur) }}" class="table-warning">Nilai Per Unsur</th>
                        <th rowspan="2" class="align-middle">IKM</th>
                        <th rowspan="2" class="align-middle">Kategori</th>
                        <th rowspan="2" class="align-middle">Jumlah Responden</th>
                        <th rowspan="2" class="align-middle">Metode SKM</th>
                        <th rowspan="2" class="align-middle">Unsur Prioritas Perbaikan</th>
                        <th rowspan="2" class="align-middle">Rencana Tindak Lanjut</th>
                        <th rowspan="2" class="align-middle" style="width:90px">Detail</th>
                    </tr>
                    <tr>
                        @foreach ($kodeUnsur as $kode)
                            <th class="table-warning">{{ $kode }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekap as $i => $baris)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="text-start">{{ $baris['puskesmas'] }}</td>
                            <td>{{ $periode->nama }}</td>
                            @foreach ($kodeUnsur as $kode)
                                <td>{{ number_format($baris['per_unsur'][$kode]['nrr_skala_100'] ?? 0, 2) }}</td>
                            @endforeach
                            <td class="fw-semibold">{{ $baris['nilai_akhir_skm'] }}</td>
                            <td>{{ $baris['mutu_akhir'] }}</td>
                            <td>{{ $baris['jumlah_responden'] }}</td>
                            <td>SKM Online</td>
                            <td class="text-start">{{ $baris['unsur_prioritas'] }}</td>
                            <td>-</td>
                            <td>
                                <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $baris['puskesmas_id'], 'periode_survei_id' => $periode->id]) }}"
                                   class="btn btn-sm btn-outline-primary">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ 10 + count($kodeUnsur) }}" class="text-center text-muted">Belum ada unit aktif</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-muted small">
            Kolom "Unsur Prioritas Perbaikan" dan "Rencana Tindak Lanjut" masih diisi manual (placeholder "-")
            — belum ada field khusus untuk itu di sistem. Kasih tahu kalau mau ditambahkan sebagai catatan
            yang bisa diedit dinkes per unit per periode.
        </p>
    @endif
@endsection
