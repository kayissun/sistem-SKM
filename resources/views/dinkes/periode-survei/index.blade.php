@extends('layouts.dinkes')

@section('title', 'Periode Survei')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Periode Survei</h3>
        <a href="{{ route('dinkes.periode-survei.create') }}" class="btn btn-primary btn-sm">+ Tambah periode</a>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Nama periode</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th style="width:100px">Status</th>
                <th style="width:160px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarPeriode as $periode)
                <tr>
                    <td>{{ $periode->nama }}</td>
                    <td>{{ $periode->tanggal_mulai->format('d M Y') }}</td>
                    <td>{{ $periode->tanggal_selesai->format('d M Y') }}</td>
                    <td>
                        @if ($periode->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('dinkes.periode-survei.edit', $periode) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('dinkes.periode-survei.destroy', $periode) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus periode ini?')">
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

    {{ $daftarPeriode->links() }}
@endsection
