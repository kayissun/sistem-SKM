@extends('layouts.puskesmas')

@section('title', 'Tindak Lanjut')

@section('content')
    <style>
        .sp-btn-cta {
            display: inline-flex; align-items: center;
            background: linear-gradient(135deg,#7C3AED,#4C1D95);
            color: #fff; border: none; border-radius: 10px;
            padding: 6px 14px; font-size: .78rem; font-weight: 600;
            text-decoration: none;
        }
        .sp-btn-cta:hover { filter: brightness(1.05); color: #fff; }
        .sp-badge-count {
            background: #EDE9FE; color: #6D28D9; font-weight: 600;
            padding: 5px 14px; border-radius: 999px; font-size: .8rem;
        }
        .sp-badge-neutral {
            background: #F3EEFF; color: #4C1D95; border: 1px solid #EDE9FE; font-weight: 500;
        }
        .sp-text-blue { color: #0D6EFD; }
        .sp-badge-blue { background: rgba(13,110,253,.1); color: #0D6EFD; }
    </style>

    <div class="sp-page-head">
        <div>
            <h3>Tindak Lanjut</h3>
            <p>Lihat nilai IKM per unsur dan ajukan tindak lanjut untuk unsur yang perlu diperbaiki.</p>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card sp-filter-card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-calendar-days me-1"></i> Periode Survei SKM
                    </label>
                    <select name="periode_survei_id" class="form-select border rounded-3" onchange="this.form.submit()">
                        @foreach ($daftarPeriode as $p)
                            <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                                {{ $p->nama }} @if($p->is_active) (Aktif) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-clock me-1"></i> Filter Triwulan TL
                    </label>
                    <select name="triwulan" class="form-select border rounded-3" onchange="this.form.submit()">
                        <option value="">Semua Triwulan</option>
                        @for ($t = 1; $t <= 4; $t++)
                            <option value="{{ $t }}" @selected($triwulan == $t)>Triwulan {{ $t }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-calendar me-1"></i> Filter Tahun TL
                    </label>
                    <select name="tahun" class="form-select border rounded-3" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        @php
                            $listTahun = $tahunTersedia->isNotEmpty() 
                                ? $tahunTersedia->merge([now()->year])->unique()->sortDesc() 
                                : collect([now()->year, now()->year - 1]);
                        @endphp
                        @foreach ($listTahun as $y)
                            <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== RINGKASAN SKM ===== --}}
    @if ($hasil && $hasil['jumlah_responden'] > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100 sp-stat-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon" style="background: linear-gradient(135deg,#7C3AED,#2A0B5E)">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <div class="label">Nilai SKM Periode Ini</div>
                            <div class="value">
                                {{ $hasil['nilai_akhir_skm'] }}
                                @if ($hasilSebelumnya && $hasilSebelumnya['jumlah_responden'] > 0)
                                    @php
                                        $diffSkm = $hasil['nilai_akhir_skm'] - $hasilSebelumnya['nilai_akhir_skm'];
                                    @endphp
                                    <small class="fs-6 ms-1 {{ $diffSkm >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fa-solid {{ $diffSkm >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                        {{ $diffSkm >= 0 ? '+' : '' }}{{ number_format($diffSkm, 2) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 sp-stat-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon" style="background: linear-gradient(135deg,#7C3AED,#2A0B5E)">
                            <i class="fa-solid fa-medal"></i>
                        </div>
                        <div>
                            <div class="label">Mutu Pelayanan</div>
                            <div class="value">{{ $hasil['mutu_akhir'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 sp-stat-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon" style="background: linear-gradient(135deg,#C88719,#E4A63B)">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div>
                            <div class="label">Total Unsur Dinilai</div>
                            <div class="value">{{ count($hasil['per_unsur']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TABEL UNSUR: TERLEMAH → TERTINGGI + AKSI ===== --}}
        @php
            $sortedUnsur = collect($hasil['per_unsur'])
                ->map(fn($data, $kode) => [...$data, 'kode' => $kode])
                ->sortBy('nrr_skala_100')
                ->values();

            // Tentukan triwulan & tahun dari periode survei
            $twPeriode = $periode && $periode->tanggal_mulai ? (int) ceil($periode->tanggal_mulai->month / 3) : (int) ceil(now()->month / 3);
            $thPeriode = $periode && $periode->tanggal_mulai ? (int) $periode->tanggal_mulai->year : (int) now()->year;
        @endphp

        <div class="card sp-section-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span><i class="fa-solid fa-arrow-down-short-wide me-2" style="color:#6D28D9"></i>Prioritas Perbaikan Unsur (Terlemah &rarr; Tertinggi)</span>
                    <span class="small text-muted ms-2">Berdasarkan hasil survei {{ $periode?->nama }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="sp-table-card table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:45px" class="text-center">No</th>
                            <th style="width:70px" class="text-center">Kode</th>
                            <th>Nama Unsur</th>
                            <th style="width:90px" class="text-center">Skor Saat Ini</th>
                            <th style="width:110px" class="text-center">Tren SKM</th>
                            <th style="width:120px" class="text-center">Kategori</th>
                            <th style="width:120px" class="text-center">Status Rencana</th>
                            <th style="width:140px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sortedUnsur as $item)
                            @php
                                $skor = $item['nrr_skala_100'] ?? 0;
                                if ($skor >= 88.31) { $level = 'success'; $label = 'Sangat Baik'; }
                                elseif ($skor >= 76.61) { $level = 'primary'; $label = 'Baik'; }
                                elseif ($skor >= 65.00) { $level = 'warning'; $label = 'Kurang Baik'; }
                                else { $level = 'danger'; $label = 'Perlu Perbaikan'; }

                                $unsurModel = $unsurAktif->firstWhere('kode', $item['kode']);
                                
                                // Cek apakah sudah ada TL untuk unsur ini di triwulan/tahun periode ini
                                $sudahAdaTl = $tindakLanjuts->first(function($tl) use ($unsurModel, $twPeriode, $thPeriode) {
                                    return $tl->unsur_pelayanan_id == ($unsurModel?->id)
                                        && $tl->triwulan == $twPeriode
                                        && $tl->tahun == $thPeriode;
                                });

                                // Perbandingan dengan periode sebelumnya jika ada
                                $skorSebelumnya = null;
                                if ($hasilSebelumnya && isset($hasilSebelumnya['per_unsur'][$item['kode']])) {
                                    $skorSebelumnya = $hasilSebelumnya['per_unsur'][$item['kode']]['nrr_skala_100'] ?? null;
                                }
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td class="text-center fw-bold" style="color:#180733">{{ $item['kode'] }}</td>
                                <td>
                                    <div class="fw-semibold" style="color:#180733;font-size:.82rem">{{ $unsurModel->nama_unsur ?? $item['kode'] }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $level === 'primary' ? 'sp-text-blue' : 'text-'.$level }}">{{ number_format($skor, 1) }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($skorSebelumnya !== null)
                                        @php $diff = $skor - $skorSebelumnya; @endphp
                                        @if ($diff > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success" title="Naik dari {{ number_format($skorSebelumnya, 1) }}">
                                                <i class="fa-solid fa-arrow-up me-1"></i>+{{ number_format($diff, 1) }}
                                            </span>
                                        @elseif ($diff < 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger" title="Turun dari {{ number_format($skorSebelumnya, 1) }}">
                                                <i class="fa-solid fa-arrow-down me-1"></i>{{ number_format($diff, 1) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-muted">Tetap</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $level === 'primary' ? 'sp-badge-blue' : 'bg-'.$level.' bg-opacity-10 text-'.$level }}" style="font-size:.72rem">{{ $label }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($sudahAdaTl)
                                        <span class="badge-tl {{ $sudahAdaTl->status_badge_class }}">{{ $sudahAdaTl->status_label }}</span>
                                    @else
                                        <span class="text-muted small">Belum diajukan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($sudahAdaTl)
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('puskesmas.tindak-lanjut.show', $sudahAdaTl) }}" class="sp-icon-btn" title="Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if ($sudahAdaTl->isEditable())
                                                <a href="{{ route('puskesmas.tindak-lanjut.edit', $sudahAdaTl) }}" class="sp-icon-btn" title="Edit Rencana">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form method="POST" action="{{ route('puskesmas.tindak-lanjut.submit', $sudahAdaTl) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="sp-icon-btn" title="Kirim ke Dinkes" style="color:#E4A63B;border-color:rgba(228,166,59,.35)" onclick="return confirm('Kirim tindak lanjut ini ke Dinkes?')">
                                                        <i class="fa-solid fa-paper-plane"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <a href="{{ route('puskesmas.tindak-lanjut.create', ['periode_survei_id' => $periode?->id, 'unsur' => $item['kode'], 'triwulan' => $twPeriode, 'tahun' => $thPeriode]) }}"
                                           class="sp-btn-cta">
                                            <i class="fa-solid fa-plus me-1"></i> Ajukan Rencana
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

    {{-- ===== DAFTAR TINDAK LANJUT YANG SUDAH DIBUAT ===== --}}
    <div class="card sp-section-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-list-check me-2" style="color:#6D28D9"></i>Daftar Rencana Tindak Lanjut</span>
            <span class="sp-badge-count">{{ $tindakLanjuts->count() }} item</span>
        </div>
        <div class="table-responsive">
            @if ($tindakLanjuts->isEmpty())
                <div class="sp-empty-state text-center py-4 text-muted">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                    Belum ada rencana tindak lanjut. Pilih unsur di atas untuk membuat rencana perbaikan.
                </div>
            @else
                <table class="sp-table-card table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px">No</th>
                            <th>Unsur Pelayanan</th>
                            <th>Triwulan</th>
                            <th>Tahun</th>
                            <th>Nilai Awal</th>
                            <th>Status</th>
                            <th>Foto Kondisi</th>
                            <th>Progres Kegiatan</th>
                            <th style="width:140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tindakLanjuts as $i => $tl)
                            @php
                                $totalProgress = $tl->progress->count();
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold" style="color:#180733">{{ $tl->unsurPelayanan->kode ?? '-' }}</div>
                                    <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                </td>
                                <td><span class="badge sp-badge-neutral">TW-{{ $tl->triwulan }}</span></td>
                                <td>{{ $tl->tahun }}</td>
                                <td class="fw-bold">{{ $tl->nilai_kondisi !== null ? number_format($tl->nilai_kondisi, 1) : '-' }}</td>
                                <td><span class="badge-tl {{ $tl->status_badge_class }}">{{ $tl->status_label }}</span></td>
                                <td>
                                    @if ($tl->foto && count($tl->foto) > 0)
                                        <span class="badge" style="background:#FCF1DC;color:#A66A0E;border:1px solid #F0DFB2;">
                                            <i class="fa-solid fa-camera me-1"></i>{{ count($tl->foto) }} foto
                                        </span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($totalProgress > 0)
                                        <a href="{{ route('puskesmas.tindak-lanjut.show', $tl) }}" class="badge bg-success bg-opacity-10 text-success text-decoration-none border border-success border-opacity-25">
                                            <i class="fa-solid fa-images me-1"></i>{{ $totalProgress }} update kegiatan
                                        </a>
                                    @else
                                        <span class="small text-muted">Belum ada progres</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('puskesmas.tindak-lanjut.show', $tl) }}" class="sp-icon-btn" title="Detail & Tambah Progres">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if ($tl->isEditable())
                                            <a href="{{ route('puskesmas.tindak-lanjut.edit', $tl) }}" class="sp-icon-btn" title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form method="POST" action="{{ route('puskesmas.tindak-lanjut.submit', $tl) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="sp-icon-btn" title="Kirim ke Dinkes" style="color:#C88719;border-color:rgba(200,135,25,.35)" onclick="return confirm('Kirim tindak lanjut ini ke Dinkes?')">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection