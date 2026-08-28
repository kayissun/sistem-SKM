@extends('layouts.puskesmas')

@section('title', 'Edit Unit Layanan')

@section('content')
    <style>
        .sp-form-card { border-radius: 14px; }
        .sp-form-card .form-label { font-size: .82rem; font-weight: 600; color: #635C7A; }
        .sp-form-card .form-control, .sp-form-card .form-select { border-radius: 10px; border-color: #E4DEF7; }
        .sp-form-card .form-control:focus, .sp-form-card .form-select:focus { border-color: #A78BFA; box-shadow: 0 0 0 .2rem rgba(167,139,250,.15); }
    </style>

    <div class="mb-4">
        <a href="{{ route('puskesmas.unit-layanan.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
        <h3 class="mt-2 fw-bold" style="color:#180733;">Edit {{ $unitLayanan->nama }}</h3>
    </div>

    <div class="card sp-form-card border-0 shadow-sm" style="max-width:500px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('puskesmas.unit-layanan.update', $unitLayanan) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Unit Layanan</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $unitLayanan->nama) }}" required>
                </div>
                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($unitLayanan->is_active)>
                    <label class="form-check-label fw-semibold" for="is_active">Aktif (ditampilkan di dropdown survei)</label>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary fw-medium rounded-3 px-4">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('puskesmas.unit-layanan.index') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
