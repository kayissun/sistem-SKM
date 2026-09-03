@extends('layouts.puskesmas')

@section('title', 'Buat Tindak Lanjut')

@section('content')
    <style>
        .sp-btn-cta {
            display: inline-flex; align-items: center; gap: 6px;
            background: linear-gradient(135deg,#7C3AED,#4C1D95);
            color: #fff; border: none; border-radius: 10px;
            padding: 10px 20px; font-size: .88rem; font-weight: 600;
            text-decoration: none;
        }
        .sp-btn-cta:hover { filter: brightness(1.05); color: #fff; }
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
            width: 80px; height: 80px; object-fit: cover;
            border-radius: 10px; border: 2px solid #E4DEF7;
        }
        .unsur-info-card {
            background: linear-gradient(135deg,#FAF8FF,#F3EEFF);
            border: 1px solid rgba(109,40,217,.12);
            border-radius: 12px;
            padding: 16px;
        }
    </style>

    <div class="mb-4">
        <a href="{{ route('puskesmas.tindak-lanjut.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
        <h3 class="mt-2" style="color:#180733;font-weight:800;">
            <i class="fa-solid fa-pen-to-square me-2" style="color:#6D28D9"></i>Buat Tindak Lanjut Baru
        </h3>
        <p class="text-muted" style="font-size:.88rem;">Laporkan unsur pelayanan yang perlu diperbaiki beserta rencana tindakan.</p>
    </div>

    @php
        $selectedKode = request('unsur');
        $selectedUnsur = $selectedKode ? $unsurAktif->firstWhere('kode', $selectedKode) : null;
        $selectedUnsurId = old('unsur_pelayanan_id', $selectedUnsur?->id);
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('puskesmas.tindak-lanjut.store') }}" enctype="multipart/form-data">
                @csrf
                @if ($periode)
                    <input type="hidden" name="periode_survei_id" value="{{ $periode->id }}">
                @endif

                {{-- Unsur Pelayanan (pre-fill dari index) --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-list me-2"></i>Form Rencana Tindak Lanjut
                        @if ($periode)
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ $periode->nama }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-layer-group me-1 text-primary"></i> Unsur Pelayanan <span class="text-danger">*</span>
                                </label>
                                @if ($selectedUnsur)
                                    {{-- Unsur sudah dipilih dari index --}}
                                    <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#F3EEFF;border:1px solid #C4B5FD;">
                                        <div class="fw-bold fs-4" style="color:#7C3AED;">{{ $selectedUnsur->kode }}</div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold" style="color:#180733">{{ $selectedUnsur->nama_unsur }}</div>
                                            @if ($hasil && isset($hasil['per_unsur'][$selectedUnsur->kode]))
                                                <div class="small text-muted">
                                                    Skor Saat Ini: <strong>{{ number_format($hasil['per_unsur'][$selectedUnsur->kode]['nrr_skala_100'] ?? 0, 1) }}</strong>
                                                    (Otomatis tercatat sebagai nilai awal)
                                                </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('puskesmas.tindak-lanjut.create', ['periode_survei_id' => $periode?->id]) }}" class="btn btn-sm btn-outline-secondary" title="Ganti unsur">
                                            Ganti
                                        </a>
                                    </div>
                                    <input type="hidden" name="unsur_pelayanan_id" value="{{ $selectedUnsurId }}">
                                @else
                                    {{-- Manual pilih --}}
                                    <select name="unsur_pelayanan_id" class="form-select @error('unsur_pelayanan_id') is-invalid @enderror" required>
                                        <option value="">— Pilih Unsur Pelayanan —</option>
                                        @foreach ($unsurAktif as $unsur)
                                            @php
                                                $skor = $hasil['per_unsur'][$unsur->kode]['nrr_skala_100'] ?? null;
                                                $sudahAda = $unsurSudahAda->contains($unsur->id);
                                            @endphp
                                            <option value="{{ $unsur->id }}"
                                                @if($sudahAda) disabled @endif
                                                @selected($selectedUnsurId == $unsur->id)>
                                                {{ $unsur->kode }} — {{ $unsur->nama_unsur }}
                                                @if($skor !== null) (Skor: {{ number_format($skor, 1) }}) @endif
                                                @if($sudahAda) ✓ Sudah ada rencana @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('unsur_pelayanan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-clock me-1 text-primary"></i> Triwulan Periode <span class="text-danger">*</span>
                                </label>
                                <select name="triwulan" class="form-select @error('triwulan') is-invalid @enderror" required>
                                    @for ($t = 1; $t <= 4; $t++)
                                        <option value="{{ $t }}" @selected(old('triwulan', $triwulanDipilih) == $t)>Triwulan {{ $t }}</option>
                                    @endfor
                                </select>
                                @error('triwulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-calendar me-1 text-primary"></i> Tahun <span class="text-danger">*</span>
                                </label>
                                <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                                    @for ($y = now()->year + 1; $y >= now()->year - 2; $y--)
                                        <option value="{{ $y }}" @selected(old('tahun', $tahunDipilih) == $y)>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-pen me-1 text-primary"></i> Rencana Tindakan Perbaikan <span class="text-danger">*</span>
                                </label>
                                <textarea name="tindakan_perbaikan" class="form-control @error('tindakan_perbaikan') is-invalid @enderror"
                                          rows="5" required
                                          placeholder="Contoh: Mengadakan pelatihan 3S (Senyum, Sapa, Salam) bagi seluruh staf loket pendaftaran dan customer service...">{{ old('tindakan_perbaikan') }}</textarea>
                                @error('tindakan_perbaikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text text-muted">Tuliskan rencana kegiatan perbaikan secara jelas dan realistis.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upload Foto / Dokumentasi --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header">
                        <i class="fa-solid fa-camera me-2"></i>Dokumentasi / Foto Bukti
                    </div>
                    <div class="card-body">
                        <div class="upload-zone" id="upload-zone" onclick="document.getElementById('input-foto').click()">
                            <i class="fa-solid fa-cloud-arrow-up d-block mb-2"></i>
                            <div class="fw-semibold" style="color:#180733;">Klik atau seret foto ke sini</div>
                            <div class="small text-muted">Maks 5 foto &middot; JPG/PNG &middot; Maks 2MB per foto</div>
                            <input type="file" name="foto[]" id="input-foto" multiple accept="image/*" class="d-none" onchange="previewFoto(this)">
                        </div>
                        <div id="preview-container" class="d-flex flex-wrap gap-2 mt-3"></div>
                        @error('foto') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        @error('foto.0') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('puskesmas.tindak-lanjut.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="sp-btn-cta">
                        <i class="fa-solid fa-save"></i> Simpan Draft
                    </button>
                </div>
            </form>
        </div>

        {{-- Sidebar Tips --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="background:#FAF8FF;">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Tips Pengisian</h6>
                    <ul class="small mb-0 text-muted" style="list-style:none;padding:0;">
                        <li class="mb-2"><i class="fa-solid fa-check-circle me-1" style="color:#C88719"></i> Jelaskan rencana perbaikan secara spesifik.</li>
                        <li class="mb-2"><i class="fa-solid fa-check-circle me-1" style="color:#C88719"></i> Upload foto dokumentasi kondisi saat ini.</li>
                        <li class="mb-0"><i class="fa-solid fa-check-circle me-1" style="color:#C88719"></i> Klik <strong>Kirim</strong> untuk submit ke Dinkes setelah selesai.</li>
                    </ul>
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
                zone.querySelector('div.fw-semibold').textContent = 'Klik atau seret foto ke sini';
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