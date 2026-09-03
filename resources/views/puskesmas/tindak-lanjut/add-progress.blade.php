@extends('layouts.puskesmas')

@section('title', 'Tambah Dokumentasi Progres')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .upload-zone {
            border: 2px dashed #C4B5FD;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            background: #FAF8FF;
            transition: .2s;
            cursor: pointer;
        }
        .upload-zone:hover { border-color: #7C3AED; background: #F3EEFF; }
        .upload-zone.has-files { border-color: #7C3AED; background: #F3EEFF; }
        .upload-zone i { font-size: 2rem; color: #7C3AED; }
        .preview-img {
            width: 85px; height: 85px; object-fit: cover;
            border-radius: 10px; border: 2px solid #E4DEF7;
        }
    </style>

    <div class="mb-4">
        <a href="{{ route('puskesmas.tindak-lanjut.show', $tindakLanjut) }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Detail
        </a>
        <h3 class="mt-2" style="color:#180733;font-weight:800;">
            <i class="fa-solid fa-camera-retro me-2"></i>Tambah Dokumentasi Progres Kegiatan
        </h3>
        <p class="text-muted" style="font-size:.88rem;">
            {{ $tindakLanjut->unsurPelayanan->kode }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur }}
            &middot; Triwulan {{ $tindakLanjut->triwulan }} {{ $tindakLanjut->tahun }}
        </p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('puskesmas.tindak-lanjut.progress.store', $tindakLanjut) }}" enctype="multipart/form-data">
                @csrf
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header fw-semibold">
                        <i class="fa-solid fa-clipboard-check me-2"></i>Laporan Pelaksanaan Kegiatan
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-camera me-1 text-primary"></i> Foto Dokumentasi Kegiatan <span class="text-danger">*</span>
                                </label>
                                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('input-foto').click()">
                                    <i class="fa-solid fa-cloud-arrow-up d-block mb-2"></i>
                                    <div class="fw-semibold" style="color:#180733;">Klik untuk memilih foto dokumentasi</div>
                                    <div class="small text-muted">Bisa pilih beberapa foto &middot; JPG, JPEG, PNG &middot; Maks 2MB per foto</div>
                                    <input type="file" name="foto[]" id="input-foto" multiple accept="image/*" class="d-none" onchange="previewFoto(this)" required>
                                </div>
                                <div id="preview-container" class="d-flex flex-wrap gap-2 mt-3"></div>
                                @error('foto') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                @error('foto.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-pen-to-square me-1 text-primary"></i> Keterangan / Deskripsi Kegiatan
                                </label>
                                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                          rows="4" placeholder="Ceritakan kegiatan yang telah dilakukan, misalnya: Melaksanakan briefing pagi dan pelatihan senyum 3S kepada seluruh petugas pendaftaran...">{{ old('keterangan') }}</textarea>
                                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text text-muted">Jelaskan kegiatan atau progres apa yang telah diterapkan.</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2 justify-content-end bg-white border-top-0 pt-0 pb-3 pe-3">
                        <a href="{{ route('puskesmas.tindak-lanjut.show', $tindakLanjut) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Simpan Dokumentasi
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3" style="background:#FAF8FF;">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Informasi Progres</h6>
                    <ul class="small mb-0 text-muted ps-3">
                        <li class="mb-2">Progres tindak lanjut berfokus pada <strong>laporan bukti kegiatan fisik/dokumentasi foto</strong> di lapangan.</li>
                        <li class="mb-2">Anda tidak perlu menginput nilai SKM manual di sini.</li>
                        <li class="mb-2">Peningkatan nilai SKM akan <strong>terhitung secara otomatis</strong> pada survei masyarakat di triwulan berikutnya.</li>
                        <li class="mb-0">Dokumentasi ini dapat dipantau langsung oleh Dinas Kesehatan.</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold small text-muted">
                    <i class="fa-solid fa-bullseye me-1"></i> Rencana Tindakan yang Dijalankan
                </div>
                <div class="card-body">
                    <p class="small text-dark mb-0" style="white-space: pre-line;">{{ $tindakLanjut->tindakan_perbaikan }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewFoto(input) {
            const container = document.getElementById('preview-container');
            const zone = document.getElementById('upload-zone');
            container.innerHTML = '';
            if (input.files.length > 0) {
                zone.classList.add('has-files');
                zone.querySelector('div.fw-semibold').textContent = input.files.length + ' foto dipilih';
            } else {
                zone.classList.remove('has-files');
                zone.querySelector('div.fw-semibold').textContent = 'Klik untuk memilih foto dokumentasi';
            }
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-img';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
@endsection