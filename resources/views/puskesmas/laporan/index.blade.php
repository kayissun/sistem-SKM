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
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="laporan-tab" data-bs-toggle="tab" data-bs-target="#tab-laporan" type="button" role="tab">Laporan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="responden-tab" data-bs-toggle="tab" data-bs-target="#tab-responden" type="button" role="tab">Data Responden</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab-laporan" role="tabpanel">
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

                <div class="card d-inline-block mb-3">
                    <div class="card-body">
                        <div class="text-muted small">Nilai akhir SKM (seluruh layanan)</div>
                        <div class="fs-3 fw-bold">{{ $hasil['nilai_akhir_skm'] }}</div>
                        <div>{{ $hasil['mutu_akhir'] }}</div>
                    </div>
                </div>

                <h5 class="mb-2">Indeks Kepuasan Masyarakat — Seluruh Layanan</h5>
                @include('partials.matriks-skm', ['hasil' => $hasil, 'judul' => $puskesmas->nama, 'id' => 'tabel-seluruh-layanan'])

                <h5 class="mb-2">IKM per Poli / Unit Layanan</h5>
                @if ($hasilPerPoli->isEmpty())
                    <p class="text-muted small mb-4">
                        Belum ada unit layanan/poli terdaftar. Tambahkan lewat menu
                        <a href="{{ route('puskesmas.unit-layanan.index') }}">Unit Layanan</a> supaya
                        laporan bisa dipecah per poli.
                    </p>
                @else
                    <div class="accordion mb-4" id="accordionPoli">
                        @foreach ($hasilPerPoli as $i => $poli)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#poli{{ $i }}">
                                        {{ $poli['unit_layanan_nama'] }}
                                        <span class="text-muted ms-2 small">
                                            &middot; {{ $poli['jumlah_responden'] }} responden
                                            @if ($poli['jumlah_responden'] > 0)
                                                &middot; IKM {{ $poli['nilai_akhir_skm'] }} ({{ $poli['mutu_akhir'] }})
                                            @endif
                                        </span>
                                    </button>
                                </h2>
                                <div id="poli{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#accordionPoli">
                                    <div class="accordion-body">
                                        @include('partials.matriks-skm', ['hasil' => $poli, 'judul' => $poli['unit_layanan_nama'], 'id' => 'tabel-poli-' . $i])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

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
                                            <a href="{{ route('puskesmas.laporan.jawaban-teks', ['pertanyaan' => $tambahan['id'], 'periode_survei_id' => $periode->id]) }}"
                                               class="btn btn-sm btn-outline-primary">Lihat jawaban</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="tab-pane" id="tab-responden" role="tabpanel">
                @include('partials.data-responden', [
                    'kodeUnsur' => $kodeUnsur ?? [],
                    'daftarResponden' => $daftarResponden ?? collect(),
                    'respondenRows' => $respondenRows ?? collect(),
                ])
            </div>
        </div>
    @endif
@endsection
