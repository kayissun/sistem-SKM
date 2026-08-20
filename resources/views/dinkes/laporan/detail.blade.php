@extends('layouts.dinkes')

@section('title', 'Detail Laporan')

@section('content')

    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: #180733; margin: 0; }
        .sp-page-head p { margin: 2px 0 0; color: #635C7A; font-size: .88rem; }

        .sp-skm-card {
            background: linear-gradient(135deg, #7C3AED, #2A0B5E);
            color: #fff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 25px -5px rgba(109, 40, 217, 0.3);
        }

        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: #FAF8FF; }

        .sp-accordion .accordion-item { border: 1px solid #E4DEF7; border-radius: 12px !important; overflow: hidden; margin-bottom: 12px; }
        .sp-accordion .accordion-button { font-weight: 700; color: #180733; background: #fff; }
        .sp-accordion .accordion-button:not(.collapsed) { background: #FAF8FF; color: #6D28D9; box-shadow: none; }
    </style>

    <!-- Navigation Back & Header -->
    <div class="mb-3">
        <a href="{{ route('dinkes.laporan.index', ['periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-light border text-secondary rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Rekap
        </a>
    </div>

    <div class="sp-page-head">
        <div>
            <h3>{{ $puskesmas->nama }}</h3>
            <p>Periode: <strong>{{ $periode->nama }}</strong> &middot; Total Responden: <strong>{{ number_format($hasil['jumlah_responden']) }}</strong> orang</p>
        </div>

        <!-- Group Action Buttons -->
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dinkes.laporan.detail.export-pdf', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm rounded-3">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('dinkes.laporan.detail.export-excel', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm rounded-3">
                <i class="fa-solid fa-file-excel me-1"></i> Excel
            </a>
            <a href="{{ route('dinkes.laporan.data-responden', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-primary btn-sm rounded-3">
                <i class="fa-solid fa-users me-1"></i> Responden
            </a>
            <a href="{{ route('dinkes.laporan.publikasi', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-primary btn-sm rounded-3">
                <i class="fa-solid fa-bullhorn me-1"></i> Format Publikasi
            </a>
        </div>
    </div>

    <!-- Alert Unsur Belum Terpetakan -->
    @if (!empty($hasil['unsur_belum_terpetakan']))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 p-3 d-flex gap-3 align-items-start">
            <i class="fa-solid fa-triangle-exclamation fa-lg mt-2 text-warning"></i>
            <div>
                <strong>Perhatian:</strong> Unit ini belum memiliki pertanyaan aktif untuk unsur berikut. Nilai SKM mungkin kurang akurat hingga pertanyaan ditambahkan:
                <ul class="mb-0 mt-1 ps-3">
                    @foreach ($hasil['unsur_belum_terpetakan'] as $unsur)
                        <li>{{ $unsur }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Score Summary Card -->
    <div class="row mb-4">
        <div class="col-md-5 col-lg-4">
            <div class="sp-skm-card">
                <div class="small text-white-50 text-uppercase fw-semibold mb-1">Nilai Akhir SKM (Seluruh Layanan)</div>
                <div class="display-5 fw-bold mb-1">{{ $hasil['nilai_akhir_skm'] }}</div>
                <div class="badge bg-white font-semibold fs-6 px-3 py-1 rounded-pill mb-2" style="color: #180733;">
                    {{ $hasil['mutu_akhir'] }}
                </div>
                <div class="small text-white-50"><i class="fa-solid fa-users me-1"></i> {{ number_format($hasil['jumlah_responden']) }} Responden Terdata</div>
            </div>
        </div>
    </div>

    <!-- Matriks SKM Seluruh Layanan -->
    <div class="mb-4">
        <h5 class="fw-bold mb-3" style="color:#180733"><i class="fa-solid fa-chart-pie text-purple me-2"></i> Indeks Kepuasan Masyarakat — Seluruh Layanan</h5>
        <div class="card sp-table-card border-0 shadow-sm">
            <div class="card-body p-0">
                @include('partials.matriks-skm', ['hasil' => $hasil, 'judul' => $puskesmas->nama, 'id' => 'tabel-seluruh-layanan'])
            </div>
        </div>
    </div>

    <!-- Accordion per Poli -->
    <div class="mb-4">
        <h5 class="fw-bold mb-3" style="color:#180733"><i class="fa-solid fa-hospital-user text-purple me-2"></i> IKM per Poli / Unit Layanan</h5>
        @if ($hasilPerPoli->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted">
                Belum ada poli / unit layanan terdaftar pada unit ini.
            </div>
        @else
            <div class="accordion sp-accordion" id="accordionPoliDinkes">
                @foreach ($hasilPerPoli as $i => $poli)
                    <div class="accordion-item shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#poliDinkes{{ $i }}">
                                <span>{{ $poli['unit_layanan_nama'] }}</span>
                                <span class="ms-2 badge bg-light text-secondary font-normal border">
                                    {{ $poli['jumlah_responden'] }} responden
                                    @if ($poli['jumlah_responden'] > 0)
                                        &middot; IKM {{ $poli['nilai_akhir_skm'] }} ({{ $poli['mutu_akhir'] }})
                                    @endif
                                </span>
                            </button>
                        </h2>
                        <div id="poliDinkes{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#accordionPoliDinkes">
                            <div class="accordion-body p-3">
                                @include('partials.matriks-skm', ['hasil' => $poli, 'judul' => $poli['unit_layanan_nama'], 'id' => 'tabel-poli-dinkes-' . $i])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Pertanyaan Tambahan -->
    @if (!empty($hasil['pertanyaan_tambahan']))
        <div class="mb-4">
            <h5 class="fw-bold mb-3" style="color:#180733"><i class="fa-solid fa-comments text-purple me-2"></i> Pertanyaan Tambahan</h5>
            <div class="card sp-table-card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Pertanyaan</th>
                                <th style="width:100px">Tipe</th>
                                <th style="width:140px">Jumlah Jawaban</th>
                                <th style="width:120px">Rata-rata</th>
                                <th style="width:120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil['pertanyaan_tambahan'] as $tambahan)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $tambahan['teks_pertanyaan'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $tambahan['tipe_input'] === 'teks' ? 'Teks' : 'Skala' }}</span></td>
                                    <td>{{ $tambahan['jumlah_jawaban'] }}</td>
                                    <td>{{ $tambahan['rata_rata'] ?? '-' }}</td>
                                    <td>
                                        @if ($tambahan['tipe_input'] === 'teks' && $tambahan['jumlah_jawaban'] > 0)
                                            <a href="{{ route('dinkes.laporan.jawaban-teks', ['puskesma' => $puskesmas, 'pertanyaan' => $tambahan['id'], 'periode_survei_id' => $periode->id]) }}"
                                               class="btn btn-sm btn-outline-primary rounded-3">Lihat Jawaban</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection