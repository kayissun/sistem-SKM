@extends('layouts.dinkes')

@section('title', 'Unsur Pelayanan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Master Unsur Pelayanan</h3>
        <a href="{{ route('dinkes.unsur-pelayanan.create') }}" class="btn btn-primary btn-sm">+ Tambah unsur</a>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th style="width:60px">Urutan</th>
                <th style="width:80px">Kode</th>
                <th>Pertanyaan</th>
                <th style="width:100px">Status</th>
                <th style="width:160px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarUnsur as $unsur)
                <tr>
                    <td>{{ $unsur->urutan }}</td>
                    <td>{{ $unsur->kode }}</td>
                    <td>{{ $unsur->pertanyaan }}</td>
                    <td>
                        @if ($unsur->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('dinkes.unsur-pelayanan.edit', $unsur) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('dinkes.unsur-pelayanan.destroy', $unsur) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus unsur ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
