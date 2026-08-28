@extends('layouts.puskesmas')

@section('title', 'Edit Petugas')

@section('content')
    <style>
        .sp-form-card { border-radius: 14px; }
        .sp-form-card .form-label { font-size: .82rem; font-weight: 600; color: #635C7A; }
        .sp-form-card .form-control, .sp-form-card .form-select { border-radius: 10px; border-color: #E4DEF7; }
        .sp-form-card .form-control:focus, .sp-form-card .form-select:focus { border-color: #A78BFA; box-shadow: 0 0 0 .2rem rgba(167,139,250,.15); }
    </style>

    <div class="mb-4">
        <a href="{{ route('puskesmas.petugas.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
        <h3 class="mt-2 fw-bold" style="color:#180733;">Edit {{ $petugas->name }}</h3>
    </div>

    <div class="card sp-form-card border-0 shadow-sm" style="max-width:560px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('puskesmas.petugas.update', $petugas) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $petugas->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $petugas->email) }}" required>
                </div>
                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($petugas->is_active)>
                    <label class="form-check-label fw-semibold" for="is_active">Akun aktif</label>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary fw-medium rounded-3 px-4">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('puskesmas.petugas.index') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
