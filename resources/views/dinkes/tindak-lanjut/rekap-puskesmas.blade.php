@extends('layouts.dinkes')

@section('title', 'Rekap Tindak Lanjut - ' . $puskesma->nama)

@section('content')
    <div class="mb-4">
        <a href="{{ route('dinkes.tindak-lanjut.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h3 class="mt-2" style="color:#180733;font-weight:800;">Rekap Tindak Lanjut</h3>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            <strong>{{ $puskesma->nama }}</strong> — Riwayat tindak lanjut dan progres capaiannya.
        </p>
    </div>

    <!-- Filter -->
    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold"><i class="fa-solid fa-clock me-1"></i> Triwulan</label>
                <select name="triwulan" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($t = 1; $t <= 4; $t++)
                        <option value="{{ $t }}" @selected($triwulan == $t)>TW-{{ $t }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold"><i class="fa-solid fa-calendar me-1"></i> Tahun</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('dinkes.tindak-lanjut.rekap-puskesmas', $puskesma) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    @if ($tindakLanjuts->isEmpty())
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fa-solid fa-calendar me-2"></i>
                        Triwulan {{ $triwulanGroup }} / {{ $tahunGroup }}
                    </span>
                    <span class="badge bg-primary rounded-pill">{{ $items->count() }} unsur</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-muted">Unsur</th>
                                    <th class="text-muted">Nilai Awal</th>
                                    <th class="text-muted">Status</th>
                                    <th class="text-muted">Progres</th>
                                    <th class="text-muted">Catatan Dinkes</th>
                                    <th class="text-muted">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $tl)
                                    @php
                                        $totalProgress = $tl->progress->count();
                                        $tercapaiCount = $tl->progress->where('tercapai', true)->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold" style="color:#180733">{{ $tl->unsurPelayanan->kode ?? '-' }}</div>
                                            <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                        </td>
                                        <td class="fw-bold">{{ $tl->nilai_kondisi !== null ? number_format($tl->nilai_kondisi, 1) : '-' }}</td>
                                        <td><span class="badge {{ $tl->status_badge_class }}">{{ $tl->status_label }}</span></td>
                                        <td>
                                            @if ($totalProgress > 0)
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <div class="progress flex-grow-1" style="height:6px;">
                                                        <div class="progress-bar bg-success" style="width:{{ ($tercapaiCount / $totalProgress) * 100 }}%"></div>
                                                    </div>
                                                    <span class="small text-muted">{{ $tercapaiCount }}/{{ $totalProgress }} tercapai</span>
                                                </div>
                                            @else
                                                <span class="small text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ Str::limit($tl->catatan_dinkes, 60) ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('dinkes.tindak-lanjut.show', $tl) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
