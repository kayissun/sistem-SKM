@extends('layouts.dinkes')

@section('title', 'Tindak Lanjut')

@section('content')
    <style>
        .tl-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
        .tl-head h3 { color:#180733; font-weight:800; margin:0 0 4px; }
        .tl-head p { color:#635C7A; margin:0; font-size:.88rem; }
        .badge-tl { font-weight:600; padding:.35em .7em; border-radius:99px; font-size:.75rem; }
    </style>

    <!-- Header -->
    <div class="tl-head">
        <div>
            <h3>Tindak Lanjut</h3>
            <p>Tinjau laporan tindak lanjut perbaikan dari seluruh puskesmas/RSU.</p>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted fw-semibold">Triwulan</label>
                <select name="triwulan" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($t = 1; $t <= 4; $t++)
                        <option value="{{ $t }}" @selected($triwulan == $t)>TW-{{ $t }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted fw-semibold">Tahun</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected($status === 'draft')>Draft</option>
                    <option value="submitted" @selected($status === 'submitted')>Terkirim</option>
                    <option value="approved" @selected($status === 'approved')>Disetujui</option>
                    <option value="rejected" @selected($status === 'rejected')>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Puskesmas/RSU</label>
                <select name="puskesmas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Unit</option>
                    @foreach ($daftarPuskesmas as $p)
                        <option value="{{ $p->id }}" @selected($puskesmasId == $p->id)>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        @php
            $submitted = $tindakLanjuts->where('status', 'submitted')->count();
            $approved = $tindakLanjuts->where('status', 'approved')->count();
            $rejected = $tindakLanjuts->where('status', 'rejected')->count();
        @endphp
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="small text-muted">Perlu Ditinjau</div>
                    <div class="fs-3 fw-bold text-info">{{ $submitted }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="small text-muted">Disetujui</div>
                    <div class="fs-3 fw-bold text-success">{{ $approved }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="small text-muted">Ditolak</div>
                    <div class="fs-3 fw-bold text-danger">{{ $rejected }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="small text-muted">Total</div>
                    <div class="fs-3 fw-bold" style="color:#180733">{{ $tindakLanjuts->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-list-check me-2"></i>Daftar Tindak Lanjut</span>
        </div>
        <div class="card-body p-0">
            @if ($tindakLanjuts->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                    Belum ada data tindak lanjut.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-muted" style="width:40px">No</th>
                                <th class="text-muted">Puskesmas/RSU</th>
                                <th class="text-muted">Unsur</th>
                                <th class="text-muted">Triwulan</th>
                                <th class="text-muted">Tahun</th>
                                <th class="text-muted">Nilai</th>
                                <th class="text-muted">Status</th>
                                <th class="text-muted">Progres</th>
                                <th class="text-muted" style="width:80px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tindakLanjuts as $i => $tl)
                                @php
                                    $totalProgress = $tl->progress->count();
                                    $tercapaiCount = $tl->progress->where('tercapai', true)->count();
                                @endphp
                                <tr>
                                    <td>{{ ($tindakLanjuts->currentPage() - 1) * $tindakLanjuts->perPage() + $i + 1 }}</td>
                                    <td class="fw-semibold" style="color:#180733">{{ $tl->puskesmas->nama ?? '-' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $tl->unsurPelayanan->kode ?? '-' }}</div>
                                        <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">TW-{{ $tl->triwulan }}</span></td>
                                    <td>{{ $tl->tahun }}</td>
                                    <td class="fw-bold">{{ $tl->nilai_kondisi !== null ? number_format($tl->nilai_kondisi, 1) : '-' }}</td>
                                    <td><span class="badge-tl {{ $tl->status_badge_class }}">{{ $tl->status_label }}</span></td>
                                    <td>
                                        @if ($totalProgress > 0)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;">
                                                    <div class="progress-bar bg-success" style="width:{{ ($tercapaiCount / $totalProgress) * 100 }}%"></div>
                                                </div>
                                                <span class="small text-muted">{{ $tercapaiCount }}/{{ $totalProgress }}</span>
                                            </div>
                                        @else
                                            <span class="small text-muted">-</span>
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
                <div class="d-flex justify-content-center py-3">
                    {{ $tindakLanjuts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
