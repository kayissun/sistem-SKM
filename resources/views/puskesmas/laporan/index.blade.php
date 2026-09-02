@extends('layouts.puskesmas')

@section('title', 'Laporan')

@section('content')
    <div class="sp-page-head">
        <div>
            <h3>Laporan SKM</h3>
            <p>Rekapitulasi nilai Indeks Kepuasan Masyarakat {{ $puskesmas->nama }}.</p>
        </div>
        @if ($periode && $hasil)
            <div class="d-flex gap-2">
                <a href="{{ route('puskesmas.laporan.export-pdf', ['periode_survei_id' => $periode->id]) }}" class="sp-btn-pdf">
                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('puskesmas.laporan.export-excel', ['periode_survei_id' => $periode->id]) }}" class="sp-btn-excel">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </a>
            </div>
        @endif
    </div>

    <!-- Filter & Pencarian -->
    <div class="card sp-filter-card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
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
                </div>
                <div class="col-md-7">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-link me-1"></i> Akses Cepat
                    </label>
                    <div class="d-flex gap-2 flex-wrap">
                        @if ($periode && $hasil)
                            <a href="{{ route('puskesmas.laporan.data-responden', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-primary btn-sm rounded-3">
                                <i class="fa-solid fa-users me-1"></i> Data Responden
                            </a>
                            <a href="{{ route('puskesmas.laporan.publikasi', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-secondary btn-sm rounded-3">
                                <i class="fa-solid fa-newspaper me-1"></i> Format Publikasi IKM
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (!$periode || !$hasil)
        <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 text-center">
            <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 text-warning"></i>
            <h6 class="fw-bold mb-1">Belum Ada Periode Survei</h6>
            <p class="mb-0 small text-muted">Silakan pilih periode survei di atas untuk melihat rekapitulasi laporan.</p>
        </div>
    @else
        @if (!empty($hasil['unsur_belum_terpetakan']))
            <div class="alert alert-warning border-0 rounded-4 shadow-sm p-3">
                <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Perhatian:</strong> unit ini belum punya pertanyaan aktif untuk unsur berikut,
                tambahkan lewat menu <a href="{{ route('puskesmas.pertanyaan.index') }}">Pertanyaan Survei</a> supaya nilai SKM lebih akurat:
                <ul class="mb-0 mt-1">
                    @foreach ($hasil['unsur_belum_terpetakan'] as $unsur)
                        <li>{{ $unsur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Ringkasan SKM --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100 sp-stat-card">
                    <div class="card-body">
                        <div class="icon" style="background: linear-gradient(135deg,#7C3AED,#2A0B5E)">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div class="label">Nilai SKM</div>
                        <div class="value fs-2">{{ $hasil['nilai_akhir_skm'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 sp-stat-card">
                    <div class="card-body">
                        <div class="icon" style="background: linear-gradient(135deg,#C88719,#E4A63B)">
                            <i class="fa-solid fa-medal"></i>
                        </div>
                        <div class="label">Mutu Pelayanan</div>
                        <div class="value fs-5">{{ $hasil['mutu_akhir'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 sp-stat-card">
                    <div class="card-body">
                        <div class="icon" style="background: linear-gradient(135deg,#C88719,#E4A63B)">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="label">Total Responden</div>
                        <div class="value fs-2">{{ $hasil['jumlah_responden'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mb-2 fw-bold" style="color:#180733">Indeks Kepuasan Masyarakat — Seluruh Layanan</h5>
        @include('partials.matriks-skm', ['hasil' => $hasil, 'judul' => $puskesmas->nama, 'id' => 'tabel-seluruh-layanan'])

        <h5 class="mb-2 mt-4 fw-bold" style="color:#180733">IKM per Poli / Unit Layanan</h5>
        @if ($hasilPerPoli->isEmpty())
            <div class="sp-empty-state">
                <i class="fa-solid fa-hospital"></i>
                Belum ada unit layanan/poli terdaftar. Tambahkan lewat menu
                <a href="{{ route('puskesmas.unit-layanan.index') }}">Unit Layanan</a> supaya
                laporan bisa dipecah per poli.
            </div>
        @else
            <div class="accordion mb-4" id="accordionPoli">
                @foreach ($hasilPerPoli as $i => $poli)
                    <div class="accordion-item sp-section-card mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#poli{{ $i }}">
                                {{ $poli['unit_layanan_nama'] }}
                                <span class="text-muted ms-2 small fw-normal">
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
            <h5 class="mt-4 fw-bold" style="color:#180733">Pertanyaan Tambahan</h5>
            <div class="card sp-table-card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pertanyaan</th>
                                <th style="width:100px">Tipe</th>
                                <th style="width:140px">Jumlah Jawaban</th>
                                <th style="width:140px">Rata-rata</th>
                                <th style="width:120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil['pertanyaan_tambahan'] as $tambahan)
                                <tr>
                                    <td class="fw-semibold" style="color:#180733">{{ $tambahan['teks_pertanyaan'] }}</td>
                                    <td>
                                        @if ($tambahan['tipe_input'] === 'teks')
                                            <span class="sp-badge-chip-gold">Teks</span>
                                        @else
                                            <span class="sp-badge-chip-light">Skala</span>
                                        @endif
                                    </td>
                                    <td>{{ $tambahan['jumlah_jawaban'] }}</td>
                                    <td class="fw-bold">{{ $tambahan['rata_rata'] ?? '-' }}</td>
                                    <td>
                                        @if ($tambahan['tipe_input'] === 'teks' && $tambahan['jumlah_jawaban'] > 0)
                                            <a href="{{ route('puskesmas.laporan.jawaban-teks', ['pertanyaan' => $tambahan['id'], 'periode_survei_id' => $periode->id]) }}"
                                               class="sp-icon-btn" title="Lihat jawaban">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
@endsection
