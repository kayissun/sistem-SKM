@extends('layouts.puskesmas')

@section('title', 'Pertanyaan Survei')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Pertanyaan Survei</h3>
        <a href="{{ route('puskesmas.pertanyaan.create') }}" class="btn btn-primary btn-sm">+ Tambah pertanyaan</a>
    </div>

    @if ($unsurBelumAda->isNotEmpty())
        <div class="alert alert-warning">
            <strong>Perhatian:</strong> unit ini belum punya pertanyaan aktif untuk unsur berikut,
            nilai SKM resmi akan kurang akurat sampai ditambahkan:
            <ul class="mb-0 mt-1">
                @foreach ($unsurBelumAda as $unsur)
                    <li>{{ $unsur->kode }} - {{ $unsur->pertanyaan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th style="width:60px">Urutan</th>
                <th>Pertanyaan</th>
                <th style="width:160px">Terkait unsur</th>
                <th style="width:100px">Status</th>
                <th style="width:160px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarPertanyaan as $pertanyaan)
                <tr>
                    <td>{{ $pertanyaan->urutan }}</td>
                    <td>{{ $pertanyaan->teks_pertanyaan }}</td>
                    <td>
                        @if ($pertanyaan->unsurPelayanan)
                            <span class="badge bg-primary">{{ $pertanyaan->unsurPelayanan->kode }}</span>
                        @else
                            <span class="badge bg-secondary">Tambahan</span>
                        @endif
                    </td>
                    <td>
                        @if ($pertanyaan->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('puskesmas.pertanyaan.edit', $pertanyaan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('puskesmas.pertanyaan.destroy', $pertanyaan) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus pertanyaan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada pertanyaan</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
