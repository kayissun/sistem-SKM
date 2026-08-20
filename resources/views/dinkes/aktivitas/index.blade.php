@extends('layouts.dinkes')

@section('title', 'Log Aktivitas')

@section('content')

    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: #180733; margin: 0; }
        .sp-page-head p { margin: 2px 0 0; color: #635C7A; font-size: .88rem; }

        /* Card & Scrollbar Tabel */
        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table-responsive { 
            scrollbar-width: thin; 
            scrollbar-color: #C4B5FD #F3EEFF; 
        }
        .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-track { background: #F3EEFF; }

        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: #FAF8FF; }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .65rem .85rem; white-space: nowrap; }
        .sp-table-card thead th { font-size: .72rem; }

        /* Badges status event */
        .badge-event-created { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-weight: 600; padding: .35em .65em; border-radius: 99px; font-size: .75rem; }
        .badge-event-updated { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; font-weight: 600; padding: .35em .65em; border-radius: 99px; font-size: .75rem; }
        .badge-event-deleted { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; font-weight: 600; padding: .35em .65em; border-radius: 99px; font-size: .75rem; }
        .badge-event-default { background: #F3F1FA; color: #6B6480; border: 1px solid #E4DEF7; font-weight: 600; padding: .35em .65em; border-radius: 99px; font-size: .75rem; }

        /* Style Pagination */
        .sp-pagination { display: flex; justify-content: flex-end; }
        .sp-pagination nav { margin: 0; }
        .sp-pagination .pagination { gap: 4px; margin: 0; }
        .sp-pagination .page-link {
            border: 1px solid rgba(109,40,217,.12);
            color: #180733;
            border-radius: 8px !important;
            font-size: .82rem;
            font-weight: 600;
            padding: .4rem .7rem;
        }
        .sp-pagination .page-link:hover { background: #F3EEFF; color: #2E1065; }
        .sp-pagination .page-item.active .page-link {
            background: linear-gradient(135deg,#7C3AED,#2A0B5E);
            border-color: transparent;
            color: #fff;
        }
        .sp-pagination .page-item.disabled .page-link { color: #C4BFD6; background: #fff; border-color: rgba(109,40,217,.08); }
    </style>

    <div class="sp-page-head">
        <div>
            <h3>Log Aktivitas</h3>
            <p>Riwayat aktivitas &amp; audit data unit, unsur pelayanan, periode survei, dan akun pengguna.</p>
        </div>
    </div>

    <div class="card sp-table-card border-0 shadow-sm mb-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th style="width: 150px;">Waktu</th>
                        <th style="width: 120px;">Kategori</th>
                        <th style="width: 110px;">Aksi</th>
                        <th style="width: 220px;">Objek / Target</th>
                        <th style="width: 160px;">Dilakukan Oleh</th>
                        <th>Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarAktivitas as $log)
                        <tr>
                            <!-- Nomor -->
                            <td class="text-center text-muted">
                                {{ $loop->iteration + ($daftarAktivitas->currentPage() - 1) * $daftarAktivitas->perPage() }}
                            </td>

                            <!-- Waktu -->
                            <td>
                                <span class="text-secondary fw-medium">
                                    <i class="fa-regular fa-clock me-1 opacity-75"></i>
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </span>
                            </td>

                            <!-- Kategori / Log Name -->
                            <td>
                                <span class="badge bg-light text-purple border border-purple-subtle px-2 py-1">
                                    {{ $log->log_name ?: 'Sistem' }}
                                </span>
                            </td>

                            <!-- Aksi Event -->
                            <td>
                                @php
                                    $badgeEvent = match($log->event) {
                                        'created' => 'badge-event-created',
                                        'updated' => 'badge-event-updated',
                                        'deleted' => 'badge-event-deleted',
                                        default => 'badge-event-default',
                                    };
                                    $labelEvent = match($log->event) {
                                        'created' => 'Dibuat',
                                        'updated' => 'Diubah',
                                        'deleted' => 'Dihapus',
                                        default => ucfirst($log->event),
                                    };
                                @endphp
                                <span class="{{ $badgeEvent }}">{{ $labelEvent }}</span>
                            </td>

                            <!-- Objek / Target -->
                            <td>
                                @if ($log->subject)
                                    <span class="fw-semibold" style="color: #180733;">
                                        <span class="text-muted font-normal">{{ class_basename($log->subject_type) }}:</span>
                                        "{{ $log->subject->nama ?? $log->subject->name ?? $log->subject->email ?? $log->subject_id }}"
                                    </span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>

                            <!-- Dilakukan oleh -->
                            <td>
                                <span class="fw-semibold text-dark">
                                    <i class="fa-regular fa-user text-purple me-1"></i>
                                    {{ $log->causer->name ?? 'Sistem' }}
                                </span>
                            </td>

                            <!-- Detail Perubahan (Tabel Mini Dalam Kolom) -->
                            <td class="align-middle">
                                @if ($log->event === 'updated' && !empty($log->properties['attributes']))
                                    <div class="d-inline-flex flex-nowrap gap-2">
                                        @foreach ($log->properties['attributes'] as $field => $nilaiBaru)
                                            @if ($field !== 'password')
                                                <div class="border rounded bg-white px-2 py-1 d-inline-block shadow-2xs">
                                                    <span class="fw-bold text-purple me-1">{{ $field }}:</span>
                                                    <span class="text-danger text-decoration-line-through me-1">{{ $log->properties['old'][$field] ?? '-' }}</span>
                                                    <i class="fa-solid fa-arrow-right-long text-muted mx-1 fs-8"></i>
                                                    <span class="text-success fw-semibold">{{ $nilaiBaru }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-clock-rotate-left fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                Belum ada aktivitas tercatat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 sp-pagination">
        {{ $daftarAktivitas->links('pagination::bootstrap-5') }}
    </div>

@endsection