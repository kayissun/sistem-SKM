@extends('layouts.puskesmas')

@section('title', 'Tambah Petugas')

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
        <h3 class="mt-2 fw-bold" style="color:#180733;">Tambah Petugas</h3>
        <p class="text-muted" style="font-size:.88rem;">Buat akun petugas baru. Password sementara akan dibuat otomatis.</p>
    </div>

    <div class="card sp-form-card border-0 shadow-sm" style="max-width:560px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('puskesmas.petugas.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Nama petugas" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@contoh.com" required>
                </div>
                <div class="alert alert-light border rounded-3 small text-muted mb-4">
                    <i class="fa-solid fa-info-circle me-1 text-primary"></i> Password sementara akan dibuat otomatis dan ditampilkan setelah akun tersimpan.
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary fw-medium rounded-3 px-4">
                        <i class="fa-solid fa-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('puskesmas.petugas.index') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
