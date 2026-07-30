@extends('layouts.dinkes')

@section('title', 'Edit Puskesmas / RSU')

@section('content')
    <h3 class="mb-3">Edit {{ $puskesmas->nama }}</h3>

    <form method="POST" action="{{ route('dinkes.puskesmas.update', $puskesmas) }}" class="bg-white p-4 rounded border">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Nama unit</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $puskesmas->nama) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <select name="jenis" class="form-select" required>
                    <option value="puskesmas" @selected($puskesmas->jenis === 'puskesmas')>Puskesmas</option>
                    <option value="rsu" @selected($puskesmas->jenis === 'rsu')>RSU</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">No. telepon</label>
                <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $puskesmas->no_telepon) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Alamat</label>
                <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $puskesmas->alamat) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $puskesmas->kecamatan) }}">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                           @checked($puskesmas->is_active)>
                    <label class="form-check-label" for="is_active">Unit aktif</label>
                </div>
            </div>
        </div>

        <button class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('dinkes.puskesmas.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
