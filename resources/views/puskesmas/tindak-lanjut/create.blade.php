@extends('layouts.puskesmas')

@section('title', 'Buat Tindak Lanjut')

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
            <i class="fa-solid fa-pen-to-square me-2"></i>Buat Tindak Lanjut Baru
        </h3>
        <p class="text-muted" style="font-size:.88rem;">Laporkan unsur pelayanan yang perlu diperbaiki beserta rencana tindakan.</p>
    </div>

    @php
        // Re-read old input for unsur selection
        $oldUnsur = old('unsur_pelayanan_id');
        $selectedKode = request('unsur');
        $selectedUnsurId = $oldUnsur ?: ($selectedKode ? $unsurAktif->firstWhere('kode', $selectedKode)?->id : null);
    @endphp

    <div class="row g-4">
        {{-- Form --}}
        <div class="col-lg-8">
            <form method="POST" action="{{ route('puskesmas.tindak-lanjut.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-list me-2"></i>Data Tindak Lanjut
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Pilih Unsur --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-layer-group me-1 text-primary"></i> Unsur Pelayanan <span class="text-danger">*</span>
                                </label>
                                <select name="unsur_pelayanan_id" id="select-unsur" class="form-select @error('unsur_pelayanan_id') is-invalid @enderror" required onchange="updateUnsurInfo()">
                                    <option value="">— Pilih Unsur —</option>
                                    @foreach ($unsurAktif as $unsur)
                                        @php
                                            $skor = $hasil['per_unsur'][$unsur->kode]['nrr_skala_100'] ?? null;
                                            $sudahAda = $unsurSudahAda->contains($unsur->id);
                                        @endphp
                                        <option value="{{ $unsur->id }}"
                                            data-kode="{{ $unsur->kode }}"
                                            data-nama="{{ $unsur->nama_unsur }}"
                                            data-skor="{{ $skor ?? '' }}"
                                            @if($sudahAda) disabled @endif
                                            @selected($selectedUnsurId == $unsur->id)>
                                            {{ $unsur->kode }} — {{ $unsur->nama_unsur }}
                                            @if($skor !== null) (Skor: {{ number_format($skor, 1) }}) @endif
                                            @if($sudahAda) ✓ Sudah ada TL @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('unsur_pelayanan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-clock me-1 text-primary"></i> Triwulan <span class="text-danger">*</span>
                                </label>
                                <select name="triwulan" class="form-select @error('triwulan') is-invalid @enderror" required>
                                    @for ($t = 1; $t <= 4; $t++)
                                        <option value="{{ $t }}" @selected(old('triwulan', 1) == $t)>TW-{{ $t }}</option>
                                    @endfor
                                </select>
                                @error('triwulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-calendar me-1 text-primary"></i> Tahun <span class="text-danger">*</span>
                                </label>
                                <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                                    @for ($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" @selected(old('tahun', date('Y')) == $y)>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-gauge-high me-1 text-primary"></i> Nilai Kondisi Saat Ini
                                </label>
                                <input type="number" name="nilai_kondisi" value="{{ old('nilai_kondisi') }}"
                                       class="form-control @error('nilai_kondisi') is-invalid @enderror"
                                       step="0.01" min="0" max="100" placeholder="Contoh: 55.3">
                                <div class="form-text">Skor NRR skala 100 unsur yang lemah (opsional).</div>
                                @error('nilai_kondisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-pen me-1 text-primary"></i> Rencana / Tindakan Perbaikan <span class="text-danger">*</span>
                                </label>
                                <textarea name="tindakan_perbaikan" class="form-control @error('tindakan_perbaikan') is-invalid @enderror"
                                          rows="4" required
                                          placeholder="Contoh: Melatih petugas pelayanan dalam sop penanganan keluhan, menambah perlengkapan, dll.">{{ old('tindakan_perbaikan') }}</textarea>
                                <div class="form-text">Jelaskan langkah-langkah konkret yang akan dilakukan untuk meningkatkan skor unsur ini.</div>
                                @error('tindakan_perbaikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-align-left me-1 text-primary"></i> Bukti Pendukung (Teks)
                                </label>
                                <textarea name="bukti" class="form-control @error('bukti') is-invalid @enderror"
                                          rows="2" placeholder="Contoh: Surat edaran no. xxx tentang peningkatan pelayanan...">{{ old('bukti') }}</textarea>
                                @error('bukti') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upload Foto --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header">
                        <i class="fa-solid fa-camera me-2"></i>Foto Bukti / Dokumentasi
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
                    <a href="{{ route('puskesmas.tindak-lanjut.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Simpan Draft
                    </button>
                </div>
            </form>
        </div>

        {{-- Sidebar Info --}}
        <div class="col-lg-4">
            {{-- Info Unsur yang dipilih --}}
            <div class="unsur-info-card mb-3" id="unsur-info-panel" style="display:none;">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="icon-box bg-d" id="unsur-icon" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" id="unsur-nama" style="color:#180733;font-size:.9rem;"></h6>
                        <div class="small text-muted" id="unsur-skor"></div>
                    </div>
                </div>
                <div id="unsur-pertanyaan"></div>
            </div>

            {{-- Panel Skor --}}
            @if ($hasil && $hasil['jumlah_responden'] > 0)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header"><i class="fa-solid fa-chart-bar me-2"></i>Skor Semua Unsur</div>
                    <div class="card-body">
                        @foreach ($hasil['per_unsur'] as $kode => $data)
                            @php
                                $skor = $data['nrr_skala_100'] ?? 0;
                                $warnaBar = match(true) {
                                    $skor >= 88 => 'bg-success',
                                    $skor >= 76 => 'bg-primary',
                                    $skor >= 65 => 'bg-warning',
                                    default => 'bg-danger',
                                };
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small">
                                    <span class="fw-semibold">{{ $kode }}</span>
                                    <span>{{ number_format($skor, 1) }}</span>
                                </div>
                                <div class="progress" style="height:8px;">
                                    <div class="progress-bar {{ $warnaBar }}" style="width:{{ $skor }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm" style="background:#FAF8FF;">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Tips Pengisian</h6>
                    <ul class="small mb-0 text-muted" style="list-style:none;padding:0;">
                        <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-1"></i> Pilih unsur dengan skor terendah sebagai prioritas.</li>
                        <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-1"></i> Jelaskan tindakan perbaikan secara spesifik.</li>
                        <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-1"></i> Upload foto dokumentasi kondisi / bukti perubahan.</li>
                        <li class="mb-0"><i class="fa-solid fa-check-circle text-success me-1"></i> Setelah draft, klik <strong>Kirim</strong> untuk submit ke Dinkes.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        const iconMap = {
            'U1': 'fa-file-signature', 'U2': 'fa-hand-holding-heart', 'U3': 'fa-gears',
            'U4': 'fa-shield-halved', 'U5': 'fa-headset', 'U6': 'fa-clock-rotate-left',
            'U7': 'fa-building-columns', 'U8': 'fa-star', 'U9': 'fa-chart-line',
        };
        const levelColors = { 'a': 'bg-a', 'b': 'bg-b', 'c': 'bg-c', 'd': 'bg-d' };

        function getLevel(skor) {
            if (skor >= 88) return 'a';
            if (skor >= 76) return 'b';
            if (skor >= 65) return 'c';
            return 'd';
        }

        function updateUnsurInfo() {
            const sel = document.getElementById('select-unsur');
            const panel = document.getElementById('unsur-info-panel');
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) { panel.style.display = 'none'; return; }

            const kode = opt.dataset.kode;
            const nama = opt.dataset.nama;
            const skor = parseFloat(opt.dataset.skor) || 0;
            const level = getLevel(skor);

            document.getElementById('unsur-nama').textContent = kode + ' — ' + nama;
            document.getElementById('unsur-skor').textContent = 'Skor: ' + skor.toFixed(1) + ' / 100';
            document.getElementById('unsur-icon').className = 'icon-box ' + levelColors[level];
            document.getElementById('unsur-icon').innerHTML = '<i class="fa-solid ' + (iconMap[kode] || 'fa-circle-question') + '"></i>';
            panel.style.display = 'block';
        }

        // Foto preview
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

        // Init on load
        updateUnsurInfo();
    </script>
@endsection
