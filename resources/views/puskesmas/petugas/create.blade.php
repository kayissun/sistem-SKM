@extends('layouts.puskesmas')

@section('title', 'Tambah Petugas')

@section('content')
    <h3 class="mb-3">Tambah Petugas</h3>

    <form method="POST" action="{{ route('puskesmas.petugas.store') }}" class="bg-white p-4 rounded border" style="max-width:500px">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <p class="text-muted small">Password sementara akan dibuat otomatis dan ditampilkan setelah akun tersimpan.</p>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('puskesmas.petugas.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
