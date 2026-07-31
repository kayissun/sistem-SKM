@extends('layouts.puskesmas')

@section('title', 'Tambah Unit Layanan')

@section('content')
    <h3 class="mb-3">Tambah Unit Layanan</h3>

    <form method="POST" action="{{ route('puskesmas.unit-layanan.store') }}" class="bg-white p-4 rounded border" style="max-width:500px">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama unit layanan</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="contoh: Poli Gigi" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('puskesmas.unit-layanan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
