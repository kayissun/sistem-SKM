@extends('layouts.dinkes')

@section('title', 'Rekap Tindak Lanjut - ' . $puskesma->nama)

@section('content')
    <style>
        /* ============ COLOR TOKENS ============ */
        :root {
            --purple-900: #180733;
            --purple-800: #2E1065;
            --purple-700: #6D28D9;
            --purple-100: #EDE9FE;
            --surface-1: #FAF8FF;
            --surface-2: #F3EEFF;
            --ink-muted: #625B78;
        }

        .back-link { 
            display:inline-flex; align-items:center; gap:6px; color:var(--purple-700); 
            text-decoration:none; font-weight:600; font-size:.9rem; transition:.15s;
        }
        .back-link:hover { color:var(--purple-800); }

        .page-head { margin-bottom:24px; }
        .page-head h3 { 
            color:var(--purple-900); font-weight:800; margin:8px 0 4px; font-size:1.75rem;
        }
        .page-head p { 
            color:var(--ink-muted); margin:0; font-size:.88rem;
        }

        .filter-card { 
            border:none; border-radius:14px; box-shadow:0 2px 6px rgba(46,16,101,.04);
        }
        .filter-card .form-label { 
            font-size:.72rem; color:var(--ink-muted); font-weight:700; letter-spacing:.02em;
        }
        .filter-card .form-select { 
            border-color:rgba(109,40,217,.15); background:var(--surface-1); 
            border-radius:10px; color:var(--purple-900);
        }
        .filter-card .btn-outline-secondary {
            border-color:rgba(109,40,217,.2); color:var(--ink-muted); border-radius:10px;
        }
        .filter-card .btn-outline-secondary:hover {
            background:var(--purple-100); color:var(--purple-900);
        }

        .tl-card {
            border:none; border-radius:14px; box-shadow:0 2px 6px rgba(46,16,101,.04);
            overflow:hidden; transition:.15s;
        }
        .tl-card:hover { box-shadow:0 6px 16px rgba(46,16,101,.08); }

        .tl-card .card-header {
            background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1);
            padding:1rem; display:flex; justify-content:space-between; align-items:center;
        }
        .tl-card .card-header span {
            font-weight:700; color:var(--purple-900);
        }

        .tl-table { font-size:.82rem; margin-bottom:0; }
        .tl-table thead {
            background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1);
        }
        .tl-table th { 
            color:var(--purple-900); font-weight:700; padding:.75rem; white-space:nowrap;
        }
        .tl-table tbody tr { border-bottom:1px solid rgba(109,40,217,.05); transition:.15s; }
        .tl-table tbody tr:hover { background:var(--surface-1); }
        .tl-table td { padding:.75rem; }
        .tl-table a { color:var(--purple-700); font-weight:600; text-decoration:none; }
        .tl-table a:hover { color:var(--purple-800); }

        .badge-status {
            display:inline-block; padding:4px 10px; border-radius:6px; 
            font-weight:600; font-size:.75rem; white-space:nowrap;
        }
        .badge-status.submitted {
            background:#DCFCE7; color:#166534; border:1px solid #BBF7D0;
        }
        .badge-status.draft {
            background:#FEF3C7; color:#854D0E; border:1px solid #FDE68A;
        }

        .progress-bar-custom {
            height:6px; border-radius:99px; overflow:hidden; background:var(--surface-2);
        }
        .progress-bar-custom .bar {
            background:linear-gradient(90deg, #10B981, #34D399); height:100%;
        }

        .alert-warning {
            background:linear-gradient(135deg, #FFFBEB, #FEF3C7);
            border:1px solid #F0DFB2; border-radius:10px;
            color:#854D0E; font-weight:500;
        }
    </style>

    <div class="page-head">
        <a href="{{ route('dinkes.tindak-lanjut.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <h3>Rekap Tindak Lanjut</h3>
        <p><strong>{{ $puskesma->nama }}</strong> — Riwayat tindak lanjut dan progres capaiannya.</p>
    </div>

    <!-- Filter -->
    <form method="GET" class="card filter-card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label"><i class="fa-solid fa-clock me-1"></i> Triwulan</label>
                <select name="triwulan" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($t = 1; $t <= 4; $t++)
                        <option value="{{ $t }}" @selected($triwulan == $t)>TW-{{ $t }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fa-solid fa-calendar me-1"></i> Tahun</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('dinkes.tindak-lanjut.rekap-puskesmas', $puskesma) }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="fa-solid fa-rotate-right me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>

    @if ($tindakLanjuts->isEmpty())
        <div class="alert alert-warning">
            <i class="fa-solid fa-circle-exclamation me-1"></i>
            Belum ada tindak lanjut dari {{ $puskesma->nama }}.
        </div>
    @else
        @php
            $grouped = $tindakLanjuts->groupBy(fn($tl) => $tl->tahun . '-' . $tl->triwulan);
        @endphp

        @foreach ($grouped as $key => $items)
            @php
                [$tahunGroup, $triwulanGroup] = explode('-', $key);
            @endphp
            <div class="card tl-card mb-4">
                <div class="card-header">
                    <span><i class="fa-solid fa-calendar me-2" style="color:var(--purple-700)"></i>Triwulan {{ $triwulanGroup }} / {{ $tahunGroup }}</span>
                    <span class="badge" style="background:var(--purple-700); color:#fff; border-radius:6px; padding:4px 10px; font-size:.75rem">
                        {{ $items->count() }} unsur
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table tl-table">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Unsur</th>
                                <th style="width:100px">Nilai Awal</th>
                                <th style="width:80px">Status</th>
                                <th style="width:140px">Progres</th>
                                <th>Catatan Dinkes</th>
                                <th style="width:70px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $tl)
                                @php
                                    $totalProgress = $tl->progress->count();
                                    $tercapaiCount = $tl->progress->where('tercapai', true)->count();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-semibold" style="color:var(--purple-900)">{{ $tl->unsurPelayanan->kode ?? '-' }}</div>
                                        <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                    </td>
                                    <td class="fw-bold">{{ $tl->nilai_kondisi !== null ? number_format($tl->nilai_kondisi, 1) : '-' }}</td>
                                    <td>
                                        <span class="badge-status {{ $tl->status === 'submitted' ? 'submitted' : 'draft' }}">
                                            {{ $tl->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($totalProgress > 0)
                                            <div style="display:flex; align-items:center; gap:8px">
                                                <div class="progress-bar-custom flex-grow-1">
                                                    <div class="bar" style="width:{{ ($tercapaiCount / $totalProgress) * 100 }}%"></div>
                                                </div>
                                                <span class="small text-muted" style="white-space:nowrap">{{ $tercapaiCount }}/{{ $totalProgress }}</span>
                                            </div>
                                        @else
                                            <span class="small text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ Str::limit($tl->catatan_dinkes, 60) ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('dinkes.tindak-lanjut.show', $tl) }}" class="btn btn-sm btn-outline-primary" title="Tinjau Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
@endsection
