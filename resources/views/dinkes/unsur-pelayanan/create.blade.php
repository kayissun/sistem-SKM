@extends('layouts.dinkes')

@section('title', 'Tambah Unsur Pelayanan')

@section('content')
    <h3 class="mb-3">Tambah Unsur Pelayanan</h3>

    <form method="POST" action="{{ route('dinkes.unsur-pelayanan.store') }}" class="bg-white p-4 rounded border" style="max-width:600px">
        @csrf
        <div class="mb-3">
            <label class="form-label">Kode</label>
            <input type="text" name="kode" class="form-control" value="{{ old('kode') }}" placeholder="contoh: U10" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pertanyaan</label>
            <input type="text" name="pertanyaan" class="form-control" value="{{ old('pertanyaan') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Urutan tampil</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $urutanBerikutnya) }}" min="1" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('dinkes.unsur-pelayanan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
