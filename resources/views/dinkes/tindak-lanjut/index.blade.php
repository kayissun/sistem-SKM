@extends('layouts.dinkes')

@section('title', 'Tindak Lanjut')

@section('content')
    <style>
        .tl-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
        .tl-head h3 { color:#180733; font-weight:800; margin:0 0 4px; }
        .tl-head p { color:#635C7A; margin:0; font-size:.88rem; }
        .unsur-header { background:#FEF3C7; }
        .badge-tl { font-weight:600; padding:.35em .7em; border-radius:99px; font-size:.75rem; }
        .faskes-card { border-left:4px solid #7C3AED; }
    </style>

    <!-- Header -->
    <div class="tl-head">
        <div>
            <h3><i class="fa-solid fa-clipboard-check me-2"></i>Monitoring Tindak Lanjut</h3>
            <p>Pantau tindak lanjut perbaikan dari seluruh puskesmas/RSU beserta capaiannya.</p>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <!-- Input Cari Faskes + Tombol Aksi -->
            <div class="col-md-5">
                <label class="form-label small text-muted fw-semibold">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Cari Faskes
                </label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Ketik nama faskes..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    @if(!empty($search) || !empty($status) || request()->has('periode_survei_id'))
                        <a href="{{ route('dinkes.tindak-lanjut.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filter Periode Survei -->
            <div class="col-md-4">
                <label class="form-label small text-muted fw-semibold">
                    <i class="fa-solid fa-calendar-days me-1"></i> Periode Survei
                </label>
                <select name="periode_survei_id" class="form-select" onchange="this.form.submit()">
                    @foreach ($daftarPeriode as $p)
                        <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                            {{ $p->nama }} @if($p->is_active) (Aktif) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status TL -->
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">
                    <i class="fa-solid fa-flag me-1"></i> Status TL
                </label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected($status === 'draft')>Draft</option>
                    <option value="submitted" @selected($status === 'submitted')>Terkirim</option>
                </select>
            </div>
        </div>
    </form>

    @if (!$periode)
        <div class="alert alert-warning">Belum ada periode survei. Buat periode terlebih dahulu.</div>
    @elseif ($dataFaskes->isEmpty())
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            Belum ada data faskes dengan responden pada periode ini.
        </div>
    @else
        {{-- Ringkasan --}}
        @php
            $totalFaskes = $dataFaskes->count();
            $totalTlAll = $dataFaskes->sum('totalTl');
            $totalTercapai = $dataFaskes->sum('tercapaiCount');
            $totalProgressAll = $dataFaskes->sum('totalProgress');
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Faskes Aktif</div>
                        <div class="fs-3 fw-bold" style="color:#180733">{{ $totalFaskes }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Total Tindak Lanjut</div>
                        <div class="fs-3 fw-bold text-info">{{ $totalTlAll }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Tercapai</div>
                        <div class="fs-3 fw-bold text-success">{{ $totalTercapai }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Total Progres</div>
                        <div class="fs-3 fw-bold" style="color:#180733">{{ $totalProgressAll }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail per Faskes --}}
        @foreach ($dataFaskes as $item)
            @php
                $puskesmas = $item['puskesmas'];
                $hasil = $item['hasil'];
                $tindakLanjuts = $item['tindakLanjuts'];
            @endphp
            <div class="card mb-4 border-0 shadow-sm faskes-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong style="color:#180733;font-size:1rem;">{{ $puskesmas->nama }}</strong>
                        <span class="small text-muted ms-2">SKM: <strong>{{ $hasil['nilai_akhir_skm'] }}</strong> — {{ $hasil['mutu_akhir'] }}</span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-primary rounded-pill">{{ $item['totalTl'] }} TL</span>
                        @if ($item['totalProgress'] > 0)
                            <span class="small text-muted">{{ $item['tercapaiCount'] }}/{{ $item['totalProgress'] }} tercapai</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    {{-- Tabel Nilai Unsur --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center align-middle bg-white mb-0" style="font-size:.78rem">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle text-start">Nama Faskes</th>
                                    <th rowspan="2" class="align-middle">SKM</th>
                                    <th colspan="{{ count($kodeUnsur) }}" class="unsur-header">Nilai Unsur Pelayanan</th>
                                </tr>
                                <tr>
                                    @foreach ($kodeUnsur as $kode)
                                        <th class="unsur-header">{{ $kode }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-semibold">{{ $puskesmas->nama }}</td>
                                    <td><strong>{{ $hasil['nilai_akhir_skm'] }}</strong></td>
                                    @foreach ($kodeUnsur as $kode)
                                        @php $skor = $hasil['per_unsur'][$kode]['nrr_skala_100'] ?? 0; @endphp
                                        <td>
                                            @if ($skor >= 88.31)
                                                <span class="text-success fw-semibold">{{ number_format($skor, 1) }}</span>
                                            @elseif ($skor >= 76.61)
                                                <span>{{ number_format($skor, 1) }}</span>
                                            @elseif ($skor >= 65.00)
                                                <span class="text-warning fw-semibold">{{ number_format($skor, 1) }}</span>
                                            @else
                                                <span class="text-danger fw-semibold">{{ number_format($skor, 1) }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Daftar TL untuk faskes ini --}}
                    @if ($tindakLanjuts->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size:.82rem">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-muted" style="width:40px">No</th>
                                        <th class="text-muted">Unsur</th>
                                        <th class="text-muted">Triwulan</th>
                                        <th class="text-muted">Rencana Perbaikan</th>
                                        <th class="text-muted">Status</th>
                                        <th class="text-muted">Progres</th>
                                        <th class="text-muted" style="width:60px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tindakLanjuts as $tl)
                                        @php
                                            $totalProg = $tl->progress->count();
                                            $tercapaiProg = $tl->progress->where('tercapai', true)->count();
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $tl->unsurPelayanan->kode ?? '-' }}</div>
                                                <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">TW-{{ $tl->triwulan }} {{ $tl->tahun }}</span></td>
                                            <td class="small">{{ Str::limit($tl->tindakan_perbaikan, 80) }}</td>
                                            <td><span class="badge-tl {{ $tl->status_badge_class }}">{{ $tl->status_label }}</span></td>
                                            <td>
                                                @if ($totalProg > 0)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height:6px;">
                                                            <div class="progress-bar bg-success" style="width:{{ ($tercapaiProg / $totalProg) * 100 }}%"></div>
                                                        </div>
                                                        <span class="small text-muted">{{ $tercapaiProg }}/{{ $totalProg }}</span>
                                                    </div>
                                                @else
                                                    <span class="small text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('dinkes.tindak-lanjut.show', $tl) }}" class="btn btn-sm btn-outline-primary" title="Tinjau">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted small">
                            Belum ada tindak lanjut dari faskes ini.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
@endsection