@extends('layouts.dinkes')

@section('title', 'Tambah Puskesmas / RSU')

@section('content')
    <h3 class="mb-3">Tambah Puskesmas / RSU</h3>

    <form method="POST" action="{{ route('dinkes.puskesmas.store') }}" class="bg-white p-4 rounded border">
        @csrf

        <h5 class="mb-3">Data unit</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nama unit</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <select name="jenis" class="form-select" required>
                    <option value="puskesmas" @selected(old('jenis') === 'puskesmas')>Puskesmas</option>
                    <option value="rsu" @selected(old('jenis') === 'rsu')>RSU</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">No. telepon</label>
                <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Alamat</label>
                <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan') }}">
            </div>
        </div>

        <h5 class="mb-3">Akun admin unit</h5>
        <p class="text-muted small">Password sementara akan dibuat otomatis dan ditampilkan setelah unit tersimpan.</p>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nama admin</label>
                <input type="text" name="admin_nama" class="form-control" value="{{ old('admin_nama') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email admin</label>
                <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}" required>
            </div>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('dinkes.puskesmas.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
