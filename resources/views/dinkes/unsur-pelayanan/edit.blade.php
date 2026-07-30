@extends('layouts.dinkes')

@section('title', 'Edit Unsur Pelayanan')

@section('content')
    <h3 class="mb-3">Edit Unsur {{ $unsur->kode }}</h3>

    <form method="POST" action="{{ route('dinkes.unsur-pelayanan.update', $unsur) }}" class="bg-white p-4 rounded border" style="max-width:600px">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Kode</label>
            <input type="text" name="kode" class="form-control" value="{{ old('kode', $unsur->kode) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pertanyaan</label>
            <input type="text" name="pertanyaan" class="form-control" value="{{ old('pertanyaan', $unsur->pertanyaan) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Urutan tampil</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $unsur->urutan) }}" min="1" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($unsur->is_active)>
            <label class="form-check-label" for="is_active">Aktif (ditampilkan di form survei)</label>
        </div>

        <button class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('dinkes.unsur-pelayanan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
