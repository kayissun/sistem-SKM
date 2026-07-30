@extends('layouts.dinkes')

@section('title', 'Log Aktivitas')

@section('content')
    <h3 class="mb-3">Log Aktivitas</h3>
    <p class="text-muted">Riwayat perubahan data unit, unsur pelayanan, periode survei, dan akun pengguna.</p>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th style="width:150px">Waktu</th>
                <th>Aksi</th>
                <th>Dilakukan oleh</th>
                <th>Perubahan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarAktivitas as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ $log->log_name }}</span>
                        {{ match($log->event) {
                            'created' => 'dibuat',
                            'updated' => 'diubah',
                            'deleted' => 'dihapus',
                            default => $log->event,
                        } }}
                        @if ($log->subject)
                            &mdash; {{ class_basename($log->subject_type) }}
                            "{{ $log->subject->nama ?? $log->subject->name ?? $log->subject->email ?? $log->subject_id }}"
                        @endif
                    </td>
                    <td>{{ $log->causer->name ?? 'Sistem' }}</td>
                    <td>
                        @if ($log->event === 'updated' && !empty($log->properties['attributes']))
                            <ul class="mb-0 small">
                                @foreach ($log->properties['attributes'] as $field => $nilaiBaru)
                                    @if ($field !== 'password')
                                        <li>
                                            <strong>{{ $field }}</strong>:
                                            {{ $log->properties['old'][$field] ?? '-' }}
                                            &rarr;
                                            {{ $nilaiBaru }}
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada aktivitas tercatat</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $daftarAktivitas->links() }}
@endsection
