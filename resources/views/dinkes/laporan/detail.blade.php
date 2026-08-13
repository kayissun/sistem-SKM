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
        <a href="{{ route('dinkes.laporan.data-responden', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-primary btn-sm">Data Responden</a>
        <a href="{{ route('dinkes.laporan.publikasi', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-secondary btn-sm">Format Publikasi IKM</a>
    </div>

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
        <p class="text-muted small mb-4">Unit ini belum punya poli/unit layanan terdaftar.</p>
    @else
        <div class="accordion mb-4" id="accordionPoliDinkes">
            @foreach ($hasilPerPoli as $i => $poli)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#poliDinkes{{ $i }}">
                            {{ $poli['unit_layanan_nama'] }}
                            <span class="text-muted ms-2 small">
                                &middot; {{ $poli['jumlah_responden'] }} responden
                                @if ($poli['jumlah_responden'] > 0)
                                    &middot; IKM {{ $poli['nilai_akhir_skm'] }} ({{ $poli['mutu_akhir'] }})
                                @endif
                            </span>
                        </button>
                    </h2>
                    <div id="poliDinkes{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#accordionPoliDinkes">
                        <div class="accordion-body">
                            @include('partials.matriks-skm', ['hasil' => $poli, 'judul' => $poli['unit_layanan_nama'], 'id' => 'tabel-poli-dinkes-' . $i])
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
                                <a href="{{ route('dinkes.laporan.jawaban-teks', ['puskesma' => $puskesmas, 'pertanyaan' => $tambahan['id'], 'periode_survei_id' => $periode->id]) }}"
                                   class="btn btn-sm btn-outline-primary">Lihat jawaban</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
