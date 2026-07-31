@extends('layouts.puskesmas')

@section('title', 'Edit Unit Layanan')

@section('content')
    <h3 class="mb-3">Edit {{ $unitLayanan->nama }}</h3>

    <form method="POST" action="{{ route('puskesmas.unit-layanan.update', $unitLayanan) }}" class="bg-white p-4 rounded border" style="max-width:500px">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama unit layanan</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $unitLayanan->nama) }}" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($unitLayanan->is_active)>
            <label class="form-check-label" for="is_active">Aktif (ditampilkan di dropdown survei)</label>
        </div>

        <button class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('puskesmas.unit-layanan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
