@extends('layouts.puskesmas')

@section('title', 'Tindak Lanjut')

@section('content')
    <div class="sp-page-head">
        <div>
            <h3><i class="fa-solid fa-clipboard-check me-2"></i>Tindak Lanjut</h3>
            <p>Lihat nilai IKM per unsur dan ajukan tindak lanjut untuk unsur yang perlu diperbaiki.</p>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card sp-filter-card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
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
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-clock me-1"></i> Triwulan
                    </label>
                    <select name="triwulan" class="form-select border rounded-3" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @for ($t = 1; $t <= 4; $t++)
                            <option value="{{ $t }}" @selected($triwulan == $t)>Triwulan {{ $t }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-calendar me-1"></i> Tahun
                    </label>
                    <select name="tahun" class="form-select border rounded-3" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                        @endfor
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
                            <div class="label">Nilai SKM Faskes</div>
                            <div class="value">{{ $hasil['nilai_akhir_skm'] }}</div>
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
                            <div class="label">Total Unsur Aktif</div>
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
        @endphp

        <div class="card sp-section-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-layer-group me-2"></i>Nilai Unsur & Aksi Tindak Lanjut</span>
                <span class="small text-muted">Diurutkan dari skor terendah ke tertinggi</span>
            </div>
            <div class="table-responsive">
                <table class="sp-table-card table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:45px" class="text-center">No</th>
                            <th style="width:70px" class="text-center">Kode</th>
                            <th>Nama Unsur</th>
                            <th style="width:80px" class="text-center">Skor</th>
                            <th style="width:110px" class="text-center">Kategori</th>
                            <th style="width:120px" class="text-center">Status TL</th>
                            <th style="width:130px" class="text-center">Aksi</th>
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
                                $sudahAdaTl = $tindakLanjuts->first(fn($tl) => $tl->unsur_pelayanan_id == ($unsurModel?->id));
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td class="text-center fw-bold" style="color:#180733">{{ $item['kode'] }}</td>
                                <td>
                                    <div class="fw-semibold" style="color:#180733;font-size:.82rem">{{ $unsurModel->nama_unsur ?? $item['kode'] }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-{{ $level }}">{{ number_format($skor, 1) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $level }} bg-opacity-10 text-{{ $level }}" style="font-size:.72rem">{{ $label }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($sudahAdaTl)
                                        <span class="badge-tl {{ $sudahAdaTl->status_badge_class }}">{{ $sudahAdaTl->status_label }}</span>
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($sudahAdaTl)
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('puskesmas.tindak-lanjut.show', $sudahAdaTl) }}" class="sp-icon-btn" title="Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if ($sudahAdaTl->isEditable())
                                                <a href="{{ route('puskesmas.tindak-lanjut.edit', $sudahAdaTl) }}" class="sp-icon-btn" title="Edit">
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
                                        <a href="{{ route('puskesmas.tindak-lanjut.create', ['periode_survei_id' => $periode?->id, 'unsur' => $item['kode']]) }}"
                                           class="btn btn-sm btn-primary rounded-3">
                                            <i class="fa-solid fa-plus me-1"></i> Ajukan TL
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
            <span><i class="fa-solid fa-list-check me-2"></i>Daftar Tindak Lanjut</span>
            <span class="badge bg-primary rounded-pill">{{ $tindakLanjuts->count() }} item</span>
        </div>
        <div class="table-responsive">
            @if ($tindakLanjuts->isEmpty())
                <div class="sp-empty-state">
                    <i class="fa-regular fa-folder-open"></i>
                    Belum ada tindak lanjut. Pilih unsur di atas atau klik <strong>Ajukan TL</strong>.
                </div>
            @else
                <table class="sp-table-card table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px">No</th>
                            <th>Unsur</th>
                            <th>Triwulan</th>
                            <th>Tahun</th>
                            <th>Nilai</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th>Progres</th>
                            <th style="width:140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tindakLanjuts as $i => $tl)
                            @php
                                $totalProgress = $tl->progress->count();
                                $tercapaiCount = $tl->progress->where('tercapai', true)->count();
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold" style="color:#180733">{{ $tl->unsurPelayanan->kode ?? '-' }}</div>
                                    <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">TW-{{ $tl->triwulan }}</span></td>
                                <td>{{ $tl->tahun }}</td>
                                <td class="fw-bold">{{ $tl->nilai_kondisi !== null ? number_format($tl->nilai_kondisi, 1) : '-' }}</td>
                                <td><span class="badge-tl {{ $tl->status_badge_class }}">{{ $tl->status_label }}</span></td>
                                <td>
                                    @if ($tl->foto && count($tl->foto) > 0)
                                        <span class="badge" style="background:#FCF1DC;color:#A66A0E;border:1px solid #F0DFB2;">
                                            <i class="fa-solid fa-camera me-1"></i>{{ count($tl->foto) }}
                                        </span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($totalProgress > 0)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px;">
                                                <div class="progress-bar bg-gold" style="width:{{ ($tercapaiCount / $totalProgress) * 100 }}%"></div>
                                            </div>
                                            <span class="small text-muted">{{ $tercapaiCount }}/{{ $totalProgress }}</span>
                                        </div>
                                    @else
                                        <span class="small text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('puskesmas.tindak-lanjut.show', $tl) }}" class="sp-icon-btn" title="Detail">
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
