@extends('layouts.puskesmas')

@section('title', 'Laporan')

@section('content')
    <h3 class="mb-3">Laporan {{ $puskesmas->nama }}</h3>

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

    @if ($periode && $hasil)
        <div class="mb-3">
            <a href="{{ route('puskesmas.laporan.export-pdf', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm">Export PDF</a>
            <a href="{{ route('puskesmas.laporan.export-excel', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm">Export Excel</a>
        </div>
    @endif

    @if (!$periode || !$hasil)
        <div class="alert alert-warning">Belum ada periode survei yang dipilih atau tersedia.</div>
    @else
        @if (!empty($hasil['unsur_belum_terpetakan']))
            <div class="alert alert-warning">
                <strong>Perhatian:</strong> unit ini belum punya pertanyaan aktif untuk unsur berikut,
                tambahkan lewat menu "Pertanyaan Survei" supaya nilai SKM lebih akurat:
                <ul class="mb-0 mt-1">
                    @foreach ($hasil['unsur_belum_terpetakan'] as $unsur)
                        <li>{{ $unsur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="text-muted">Jumlah responden: {{ $hasil['jumlah_responden'] }}</p>

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

        @if (!empty($hasil['pertanyaan_tambahan']))
            <h5 class="mt-4">Pertanyaan Tambahan (di luar nilai SKM resmi)</h5>
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>Pertanyaan</th>
                        <th style="width:100px">Tipe</th>
                        <th style="width:140px">Jumlah jawaban</th>
                        <th style="width:140px">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hasil['pertanyaan_tambahan'] as $tambahan)
                        <tr>
                            <td>{{ $tambahan['teks_pertanyaan'] }}</td>
                            <td>{{ $tambahan['tipe_input'] === 'teks' ? 'Teks' : 'Skala' }}</td>
                            <td>{{ $tambahan['jumlah_jawaban'] }}</td>
                            <td>{{ $tambahan['rata_rata'] ?? '-' }}</td>
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
    @endif
@endsection
