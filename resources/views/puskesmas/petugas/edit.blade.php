@extends('layouts.puskesmas')

@section('title', 'Edit Petugas')

@section('content')
    <h3 class="mb-3">Edit {{ $petugas->name }}</h3>

    <form method="POST" action="{{ route('puskesmas.petugas.update', $petugas) }}" class="bg-white p-4 rounded border" style="max-width:500px">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $petugas->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $petugas->email) }}" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($petugas->is_active)>
            <label class="form-check-label" for="is_active">Akun aktif</label>
        </div>

        <button class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('puskesmas.petugas.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
