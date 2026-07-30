@extends('layouts.puskesmas')

@section('title', 'Petugas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Petugas</h3>
        <a href="{{ route('puskesmas.petugas.create') }}" class="btn btn-primary btn-sm">+ Tambah petugas</a>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th style="width:100px">Status</th>
                <th style="width:160px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarPetugas as $petugas)
                <tr>
                    <td>{{ $petugas->name }}</td>
                    <td>{{ $petugas->email }}</td>
                    <td>
                        @if ($petugas->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('puskesmas.petugas.edit', $petugas) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('puskesmas.petugas.destroy', $petugas) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Nonaktifkan akun petugas ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Nonaktifkan</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada petugas</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $daftarPetugas->links() }}
@endsection
