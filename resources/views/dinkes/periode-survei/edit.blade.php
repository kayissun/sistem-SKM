@extends('layouts.dinkes')

@section('title', 'Edit Periode Survei')

@section('content')
    <h3 class="mb-3">Edit {{ $periode->nama }}</h3>

    <form method="POST" action="{{ route('dinkes.periode-survei.update', $periode) }}" class="bg-white p-4 rounded border" style="max-width:600px">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama periode</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $periode->nama) }}" required>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control"
                       value="{{ old('tanggal_mulai', $periode->tanggal_mulai->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control"
                       value="{{ old('tanggal_selesai', $periode->tanggal_selesai->format('Y-m-d')) }}" required>
            </div>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($periode->is_active)>
            <label class="form-check-label" for="is_active">Jadikan periode aktif</label>
        </div>

        <button class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('dinkes.periode-survei.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
