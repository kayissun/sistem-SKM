@extends('layouts.dinkes')

@section('title', 'Tindak Lanjut')

@section('content')
    <style>
        /* ============ COLOR TOKENS ============ */
        :root {
            --purple-900: #180733;
            --purple-800: #2E1065;
            --purple-700: #6D28D9;
            --purple-600: #7C3AED;
            --purple-500: #8B5CF6;
            --purple-100: #EDE9FE;
            --gold-700: #A66A0E;
            --gold-600: #C88719;
            --gold-400: #E4A63B;
            --gold-100: #FCF1DC;
            --surface-0: #FFFFFF;
            --surface-1: #FAF8FF;
            --surface-2: #F3EEFF;
            --ink: #14102B;
            --ink-muted: #625B78;
        }

        /* ============ PAGE HEAD ============ */
        .tl-head { 
            display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:20px;
        }
        .tl-head h3 { 
            color:var(--purple-900); font-weight:800; margin:0 0 4px; font-size:1.75rem;
        }
        .tl-head p { 
            color:var(--ink-muted); margin:0; font-size:.88rem;
        }

        /* ============ FILTER FORM ============ */
        .tl-filter { 
            border-radius:14px; border:none; 
        }
        .tl-filter .form-label { 
            font-size:.72rem; letter-spacing:.02em; color:var(--ink-muted); font-weight:700;
        }
        .tl-filter .form-control, .tl-filter .form-select { 
            border-color:rgba(109,40,217,.15); background:var(--surface-1); border-radius:10px; 
            font-size:.9rem; color:var(--purple-900);
        }
        .tl-filter .form-control:focus, .tl-filter .form-select:focus { 
            box-shadow: 0 0 0 .2rem rgba(109,40,217,.12); border-color:var(--purple-700);
        }
        .tl-filter .btn-primary { 
            background:var(--purple-700); border-color:var(--purple-700); border-radius:10px; font-weight:600;
        }
        .tl-filter .btn-primary:hover { 
            background:var(--purple-800); border-color:var(--purple-800);
        }
        .tl-filter .btn-outline-secondary {
            border-color:rgba(109,40,217,.2); color:var(--ink-muted); border-radius:10px;
        }
        .tl-filter .btn-outline-secondary:hover {
            background:var(--purple-100); color:var(--purple-900);
        }

        /* ============ STAT CARDS ============ */
        .tl-stat-card {
            border:none; border-radius:14px; box-shadow:0 2px 6px rgba(46,16,101,.04);
            border-left:4px solid var(--purple-700); transition:.15s;
        }
        .tl-stat-card:hover {
            box-shadow:0 6px 16px rgba(46,16,101,.08);
        }
        .tl-stat-card .card-body {
            padding:1.25rem;
        }
        .tl-stat-card .small {
            color:var(--ink-muted); font-size:.8rem;
        }
        .tl-stat-card .fs-3 {
            color:var(--purple-900); font-weight:800;
        }

        /* ============ FASKES CARDS & TABLES ============ */
        .faskes-card {
            border:none; border-radius:14px; box-shadow:0 2px 6px rgba(46,16,101,.04);
            overflow:hidden; transition:.15s;
        }
        .faskes-card:hover {
            box-shadow:0 6px 16px rgba(46,16,101,.08);
        }
        .faskes-card .card-header {
            background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1);
            padding:1rem;
        }
        .faskes-card .card-header strong {
            color:var(--purple-900); font-size:1rem;
        }

        /* ============ TABLE STYLING ============ */
        .tl-table { 
            font-size:.82rem; margin-bottom:0;
        }
        .tl-table thead {
            background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1);
        }
        .tl-table th { 
            color:var(--purple-900); font-weight:700; padding:.75rem .85rem; white-space:nowrap;
        }
        .tl-table tbody tr {
            transition:background .15s; border-bottom:1px solid rgba(109,40,217,.05);
        }
        .tl-table tbody tr:hover {
            background:var(--surface-1);
        }
        .tl-table td { padding:.75rem .85rem; }
        .tl-table a { 
            color:var(--purple-700); font-weight:600; text-decoration:none; transition:.15s;
        }
        .tl-table a:hover {
            color:var(--purple-800);
        }

        /* ============ BADGES & ELEMENTS ============ */
        .badge-tl { 
            font-weight:600; padding:.4em .7em; border-radius:8px; font-size:.75rem;
        }
        .badge-status {
            display:inline-block; padding:3px 10px; border-radius:6px; font-weight:600; font-size:.75rem;
        }
        .badge-status.submitted {
            background:#DCFCE7; color:#166534; border:1px solid #BBF7D0;
        }
        .badge-status.draft {
            background:#FEF3C7; color:#854D0E; border:1px solid #FDE68A;
        }

        /* ============ PROGRESS BAR ============ */
        .tl-progress {
            display:flex; align-items:center; gap:8px;
        }
        .tl-progress .progress {
            height:6px; border-radius:99px; overflow:hidden;
            background:var(--surface-2);
        }
        .tl-progress .progress-bar {
            background:linear-gradient(90deg, #10B981, #34D399); height:100%;
        }

        /* ============ EMPTY STATE ============ */
        .alert-warning {
            background:linear-gradient(135deg, #FFFBEB, #FEF3C7);
            border:1px solid var(--gold-400);
            color:var(--gold-700);
            border-radius:10px; font-weight:500;
        }
    </style>

    <!-- Header -->
    <div class="tl-head">
        <div>
            <span style="display:inline-flex; align-items:center; gap:6px; font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--purple-700); background:var(--purple-100); padding:3px 10px; border-radius:99px; margin-bottom:8px;">
                <i class="fa-solid fa-clipboard-check"></i> Monitoring
            </span>
            <h3><i class="fa-solid fa-clipboard-check me-2" style="color:var(--purple-700)"></i>Tindak Lanjut Perbaikan</h3>
            <p>Pantau tindak lanjut perbaikan dari seluruh puskesmas/RSU beserta capaiannya.</p>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="card tl-filter border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <!-- Input Cari Faskes -->
            <div class="col-md-5">
                <label class="form-label">
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
                <label class="form-label">
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
                <label class="form-label">
                    <i class="fa-solid fa-flag me-1"></i> Status
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
        <div class="alert alert-warning">
            <i class="fa-solid fa-circle-exclamation me-1"></i> Belum ada periode survei. Buat periode terlebih dahulu.
        </div>
    @elseif ($dataFaskes->isEmpty())
        <div class="alert alert-warning">
            <i class="fa-solid fa-circle-exclamation me-1"></i> Belum ada data faskes dengan responden pada periode ini.
        </div>
    @else
        {{-- Ringkasan Statistik --}}
        @php
            $totalFaskes = $dataFaskes->count();
            $totalTlAll = $dataFaskes->sum('totalTl');
            $totalProgressAll = $dataFaskes->sum('totalProgress');
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card tl-stat-card">
                    <div class="card-body text-center">
                        <div class="small">Faskes Mengisi Survei</div>
                        <div class="fs-3">{{ $totalFaskes }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tl-stat-card" style="border-left-color:var(--purple-600)">
                    <div class="card-body text-center">
                        <div class="small">Total Rencana TL</div>
                        <div class="fs-3">{{ $totalTlAll }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tl-stat-card" style="border-left-color:#10B981">
                    <div class="card-body text-center">
                        <div class="small">Total Dokumentasi Kegiatan</div>
                        <div class="fs-3" style="color:#10B981">{{ $totalProgressAll }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail per Faskes --}}
        @foreach ($dataFaskes as $item)
            @php
                $puskesmas = $item['puskesmas'];
                $hasil = $item['hasil'];
                $hasilSebelumnya = $item['hasilSebelumnya'] ?? null;
                $tindakLanjuts = $item['tindakLanjuts'];
            @endphp
            <div class="card mb-4 border-0 shadow-sm faskes-card">
                <div class="card-header">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <strong style="color:var(--purple-900); font-size:1rem;">{{ $puskesmas->nama }}</strong>
                            <span class="small text-muted ms-3">
                                <i class="fa-solid fa-chart-line me-1" style="color:var(--purple-700)"></i>
                                SKM: <strong>{{ $hasil['nilai_akhir_skm'] }}</strong> — {{ $hasil['mutu_akhir'] }}
                            </span>
                            @if ($hasilSebelumnya && $hasilSebelumnya['jumlah_responden'] > 0)
                                @php
                                    $diff = $hasil['nilai_akhir_skm'] - $hasilSebelumnya['nilai_akhir_skm'];
                                @endphp
                                <span class="badge ms-2 {{ $diff >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="font-size:.75rem">
                                    <i class="fa-solid {{ $diff >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                                    {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }} vs TW Lalu
                                </span>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge-tl badge-status submitted me-2">{{ $item['totalTl'] }} Rencana TL</span>
                            <span class="small text-muted">{{ $item['totalProgress'] }} update kegiatan</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    {{-- Tabel Tindak Lanjut (Gabungan) --}}
                    <div class="table-responsive">
                        <table class="table tl-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width:40px">No</th>
                                    <th>Unsur Pelayanan</th>
                                    <th style="width:100px">Nilai Awal</th>
                                    <th>Rencana Perbaikan</th>
                                    <th style="width:90px">TW/Tahun</th>
                                    <th style="width:80px">Status</th>
                                    <th style="width:140px">Dokumentasi</th>
                                    <th style="width:70px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tindakLanjuts as $tl)
                                    @php
                                        $totalProg = $tl->progress->count();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold" style="color:var(--purple-900)">{{ $tl->unsurPelayanan->kode ?? '-' }}</div>
                                            <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                        </td>
                                        <td class="fw-bold">{{ $tl->nilai_kondisi !== null ? number_format($tl->nilai_kondisi, 1) : '-' }}</td>
                                        <td class="small">{{ Str::limit($tl->tindakan_perbaikan, 70) }}</td>
                                        <td>
                                            <span class="badge-tl" style="background:var(--surface-2); color:var(--ink-muted)">
                                                TW-{{ $tl->triwulan }} {{ $tl->tahun }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge-status {{ $tl->status === 'submitted' ? 'submitted' : 'draft' }}">
                                                {{ $tl->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($totalProg > 0)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size:.75rem">
                                                    <i class="fa-solid fa-camera me-1"></i>{{ $totalProg }} laporan
                                                </span>
                                            @else
                                                <span class="small text-muted">— Belum ada —</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('dinkes.tindak-lanjut.show', $tl) }}" class="btn btn-sm btn-outline-primary" title="Tinjau Detail">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('dinkes.tindak-lanjut.rekap-puskesmas', $puskesmas) }}" class="btn btn-sm btn-outline-secondary" title="Rekap">
                                                    <i class="fa-solid fa-chart-pie"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($tindakLanjuts->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                            Belum ada tindak lanjut dari faskes ini.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
@endsection