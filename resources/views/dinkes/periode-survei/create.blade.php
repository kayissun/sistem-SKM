@extends('layouts.dinkes')

@section('title', 'Tambah Periode Survei')

@section('content')
    <h3 class="mb-3">Tambah Periode Survei</h3>

    <form method="POST" action="{{ route('dinkes.periode-survei.store') }}" class="bg-white p-4 rounded border" style="max-width:600px">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama periode</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="contoh: Triwulan IV 2026" required>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}" required>
            </div>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active'))>
            <label class="form-check-label" for="is_active">Jadikan periode aktif (periode aktif lain otomatis dinonaktifkan)</label>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('dinkes.periode-survei.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
