@extends('layouts.dinkes')

@section('title', 'Detail Laporan')

@section('content')
    <a href="{{ route('dinkes.laporan.index', ['periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali</a>

    <h3 class="mb-1">{{ $puskesmas->nama }}</h3>
    <p class="text-muted">Periode: {{ $periode->nama }} &middot; Responden: {{ $hasil['jumlah_responden'] }}</p>

    @if (!empty($hasil['unsur_belum_terpetakan']))
        <div class="alert alert-warning">
            <strong>Perhatian:</strong> unit ini belum punya pertanyaan aktif untuk unsur berikut,
            nilai SKM di bawah kurang akurat sampai unit menambahkannya sendiri:
            <ul class="mb-0 mt-1">
                @foreach ($hasil['unsur_belum_terpetakan'] as $unsur)
                    <li>{{ $unsur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('dinkes.laporan.detail.export-pdf', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm">Export PDF</a>
        <a href="{{ route('dinkes.laporan.detail.export-excel', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm">Export Excel</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Rincian per Unsur</h5>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="salinTabelKeClipboard('tabel-per-unsur', this)">
            Salin Tabel
        </button>
    </div>

    <table id="tabel-per-unsur" class="table table-bordered bg-white">
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

    @if (!empty($hasil['pertanyaan_tambahan']))
        <h5 class="mt-4">Pertanyaan Tambahan (di luar nilai SKM resmi)</h5>
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Pertanyaan</th>
                    <th style="width:100px">Tipe</th>
                    <th style="width:140px">Jumlah jawaban</th>
                    <th style="width:140px">Rata-rata</th>
                    <th style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil['pertanyaan_tambahan'] as $tambahan)
                    <tr>
                        <td>{{ $tambahan['teks_pertanyaan'] }}</td>
                        <td>{{ $tambahan['tipe_input'] === 'teks' ? 'Teks' : 'Skala' }}</td>
                        <td>{{ $tambahan['jumlah_jawaban'] }}</td>
                        <td>{{ $tambahan['rata_rata'] ?? '-' }}</td>
                        <td>
                            @if ($tambahan['tipe_input'] === 'teks' && $tambahan['jumlah_jawaban'] > 0)
                                <a href="{{ route('dinkes.laporan.jawaban-teks', ['puskesma' => $puskesmas, 'pertanyaan' => $tambahan['id'], 'periode_survei_id' => $periode->id]) }}"
                                   class="btn btn-sm btn-outline-primary">Lihat jawaban</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="card d-inline-block">
        <div class="card-body">
            <div class="text-muted small">Nilai akhir SKM</div>
            <div class="fs-3 fw-bold">{{ $hasil['nilai_akhir_skm'] }}</div>
            <div>{{ $hasil['mutu_akhir'] }}</div>
        </div>
    </div>
@endsection
