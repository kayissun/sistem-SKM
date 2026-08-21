@extends('layouts.dinkes')

@section('title', 'Rekap Tindak Lanjut - ' . $puskesma->nama)

@section('content')
    <div class="mb-4">
        <a href="{{ route('dinkes.tindak-lanjut.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h3 class="mt-2" style="color:#180733;font-weight:800;">Rekap Tindak Lanjut</h3>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            <strong>{{ $puskesma->nama }}</strong> — Seluruh laporan tindak lanjut yang pernah diajukan.
        </p>
    </div>

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
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height:6px;">
                                                        <div class="progress-bar bg-success" style="width:{{ ($tercapaiCount / $totalProgress) * 100 }}%"></div>
                                                    </div>
                                                    <span class="small text-muted">{{ $tercapaiCount }}/{{ $totalProgress }} tercapai</span>
                                                </div>
                                                @foreach ($tl->progress->sortBy('triwulan_target') as $prog)
                                                    <div class="small mt-1">
                                                        <span class="badge {{ $prog->tercapai ? 'bg-success' : 'bg-warning text-dark' }} me-1" style="font-size:.65rem;">
                                                            TW-{{ $prog->triwulan_target }} {{ $prog->tahun_target }}: {{ $prog->tercapai ? '✓' : '✗' }}
                                                        </span>
                                                        @if ($prog->nilai_akhir !== null)
                                                            {{ number_format($prog->nilai_akhir, 1) }}
                                                        @endif
                                                    </div>
                                                @endforeach
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
