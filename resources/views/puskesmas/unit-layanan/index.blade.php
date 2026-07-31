@extends('layouts.puskesmas')

@section('title', 'Unit Layanan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Unit Layanan / Poli</h3>
        <a href="{{ route('puskesmas.unit-layanan.create') }}" class="btn btn-primary btn-sm">+ Tambah unit layanan</a>
    </div>
    <p class="text-muted">Daftar ini akan muncul sebagai pilihan dropdown di form survei publik.</p>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Nama</th>
                <th style="width:100px">Status</th>
                <th style="width:160px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarUnitLayanan as $unit)
                <tr>
                    <td>{{ $unit->nama }}</td>
                    <td>
                        @if ($unit->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('puskesmas.unit-layanan.edit', $unit) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('puskesmas.unit-layanan.destroy', $unit) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus unit layanan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted">Belum ada unit layanan</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
