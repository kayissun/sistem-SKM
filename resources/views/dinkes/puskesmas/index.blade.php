@extends('layouts.dinkes')

@section('title', 'Puskesmas / RSU')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Puskesmas / RSU</h3>
        <a href="{{ route('dinkes.puskesmas.create') }}" class="btn btn-primary btn-sm">+ Tambah unit</a>
    </div>

    <table class="table table-bordered bg-white align-middle">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Kecamatan</th>
                <th>Jumlah akun</th>
                <th>Status</th>
                <th style="width:70px">QR</th>
                <th style="width:220px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarPuskesmas as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ strtoupper($item->jenis) }}</td>
                    <td>{{ $item->kecamatan ?? '-' }}</td>
                    <td>{{ $item->users_count }}</td>
                    <td>
                        @if ($item->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        @if ($item->is_active)
                            <img src="{{ route('qrcode.tampil', $item) }}" width="50" height="50" alt="QR {{ $item->nama }}">
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('dinkes.puskesmas.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="{{ route('survei.create', $item) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Link</a>
                        @if ($item->is_active)
                            <a href="{{ route('qrcode.unduh', $item) }}" class="btn btn-sm btn-outline-secondary">Unduh QR</a>
                        @endif
                        <form action="{{ route('dinkes.puskesmas.destroy', $item) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Nonaktifkan unit ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Nonaktifkan</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $daftarPuskesmas->links() }}
@endsection
