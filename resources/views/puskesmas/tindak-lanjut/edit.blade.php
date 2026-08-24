@extends('layouts.puskesmas')

@section('title', 'Edit Tindak Lanjut')

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
        .upload-zone.has-files { border-color: #10B981; background: #ECFDF5; }
        .upload-zone i { font-size: 2rem; color: #7C3AED; }
        .preview-img {
            width: 80px; height: 80px; object-fit: cover;
            border-radius: 10px; border: 2px solid #E4DEF7;
        }
        .existing-foto {
            position: relative; display: inline-block;
        }
        .existing-foto img {
            width: 80px; height: 80px; object-fit: cover;
            border-radius: 10px; border: 2px solid #E4DEF7;
        }
        .existing-foto .btn-hapus-foto {
            position: absolute; top: -6px; right: -6px;
            width: 22px; height: 22px;
            border-radius: 50%; border: 2px solid #fff;
            background: #DC2626; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .6rem; cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,.2);
        }
        .existing-foto.dimmed img { opacity: .3; filter: grayscale(1); }
    </style>

    <div class="mb-4">
        <a href="{{ route('puskesmas.tindak-lanjut.show', $tindakLanjut) }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Detail
        </a>
        <h3 class="mt-2" style="color:#180733;font-weight:800;">
            <i class="fa-solid fa-pen me-2"></i>Edit Tindak Lanjut
        </h3>
        <p class="text-muted" style="font-size:.88rem;">
            {{ $tindakLanjut->unsurPelayanan->kode }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur }}
            &middot; TW-{{ $tindakLanjut->triwulan }} {{ $tindakLanjut->tahun }}
        </p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('puskesmas.tindak-lanjut.update', $tindakLanjut) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Tindakan Perbaikan</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-pen me-1 text-primary"></i> Tindakan Perbaikan <span class="text-danger">*</span>
                            </label>
                            <textarea name="tindakan_perbaikan" class="form-control @error('tindakan_perbaikan') is-invalid @enderror"
                                      rows="5" required>{{ old('tindakan_perbaikan', $tindakLanjut->tindakan_perbaikan) }}</textarea>
                            @error('tindakan_perbaikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-align-left me-1 text-primary"></i> Bukti Pendukung (Teks)
                            </label>
                            <textarea name="bukti" class="form-control @error('bukti') is-invalid @enderror"
                                      rows="3">{{ old('bukti', $tindakLanjut->bukti) }}</textarea>
                            @error('bukti') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Foto --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header"><i class="fa-solid fa-camera me-2"></i>Foto Bukti / Dokumentasi</div>
                    <div class="card-body">
                        {{-- Existing photos --}}
                        @if ($tindakLanjut->foto && count($tindakLanjut->foto) > 0)
                            <div class="mb-3">
                                <div class="small fw-semibold text-muted mb-2"><i class="fa-solid fa-image me-1"></i> Foto yang sudah ada:</div>
                                <div class="d-flex flex-wrap gap-2" id="existing-photos">
                                    @foreach ($tindakLanjut->foto as $idx => $path)
                                        <div class="existing-foto" id="foto-{{ $idx }}">
                                            <img src="{{ asset('storage/' . $path) }}" alt="Foto {{ $idx + 1 }}">
                                            <button type="button" class="btn-hapus-foto" onclick="hapusFotoExisting('{{ $path }}', {{ $idx }})" title="Hapus">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Upload new --}}
                        <div class="upload-zone" onclick="document.getElementById('input-foto-baru').click()">
                            <i class="fa-solid fa-cloud-arrow-up d-block mb-2"></div>
                            <div class="fw-semibold" style="color:#180733;">Klik atau seret foto baru ke sini</div>
                            <div class="small text-muted">Maks 5 foto &middot; JPG/PNG &middot; Maks 2MB per foto</div>
                            <input type="file" name="foto_baru[]" id="input-foto-baru" multiple accept="image/*" class="d-none" onchange="previewFotoBaru(this)">
                        </div>
                        <div id="preview-baru" class="d-flex flex-wrap gap-2 mt-3"></div>
                        @error('foto_baru') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('puskesmas.tindak-lanjut.show', $tindakLanjut) }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-info-circle me-1 text-primary"></i> Info Tindak Lanjut</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width:100px">Status</td><td><span class="badge {{ $tindakLanjut->status_badge_class }}">{{ $tindakLanjut->status_label }}</span></td></tr>
                        <tr><td class="text-muted">Unsur</td><td class="fw-semibold">{{ $tindakLanjut->unsurPelayanan->kode }}</td></tr>
                        <tr><td class="text-muted">Triwulan</td><td>TW-{{ $tindakLanjut->triwulan }}</td></tr>
                        <tr><td class="text-muted">Tahun</td><td>{{ $tindakLanjut->tahun }}</td></tr>
                        <tr><td class="text-muted">Nilai</td><td>{{ $tindakLanjut->nilai_kondisi ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Foto</td><td>{{ $tindakLanjut->foto ? count($tindakLanjut->foto) : 0 }} gambar</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        let fotoHapus = [];

        function hapusFotoExisting(path, idx) {
            const el = document.getElementById('foto-' + idx);
            if (el) {
                el.classList.toggle('dimmed');
                if (el.classList.contains('dimmed')) {
                    // Tambah hidden input
                    const h = document.createElement('input');
                    h.type = 'hidden';
                    h.name = 'foto_hapus[]';
                    h.value = path;
                    h.id = 'hapus-' + idx;
                    document.querySelector('form').appendChild(h);
                } else {
                    const h = document.getElementById('hapus-' + idx);
                    if (h) h.remove();
                }
            }
        }

        function previewFotoBaru(input) {
            const container = document.getElementById('preview-baru');
            container.innerHTML = '';
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