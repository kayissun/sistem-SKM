@extends('layouts.puskesmas')

@section('title', 'Edit Pertanyaan Survei')

@section('content')
    <style>
        .sp-form-card { border-radius: 14px; }
        .sp-form-card .form-label { font-size: .82rem; font-weight: 600; color: #635C7A; }
        .sp-form-card .form-control, .sp-form-card .form-select { border-radius: 10px; border-color: #E4DEF7; }
        .sp-form-card .form-control:focus, .sp-form-card .form-select:focus { border-color: #A78BFA; box-shadow: 0 0 0 .2rem rgba(167,139,250,.15); }
    </style>

    <div class="mb-4">
        <a href="{{ route('puskesmas.pertanyaan.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h3 class="mt-2 fw-bold" style="color:#180733;">Edit Pertanyaan</h3>
    </div>

    <div class="card sp-form-card border-0 shadow-sm" style="max-width:700px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('puskesmas.pertanyaan.update', $pertanyaan) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Teks Pertanyaan</label>
                    <input type="text" name="teks_pertanyaan" class="form-control" value="{{ old('teks_pertanyaan', $pertanyaan->teks_pertanyaan) }}" placeholder="Tuliskan pertanyaan survei..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Jawaban</label>
                    <select name="tipe_input" id="tipe_input" class="form-select">
                        <option value="skala" @selected(old('tipe_input', $pertanyaan->tipe_input) === 'skala')>Skala penilaian (1-4)</option>
                        <option value="teks" @selected(old('tipe_input', $pertanyaan->tipe_input) === 'teks')>Teks bebas / masukan tertulis</option>
                    </select>
                    <div class="form-text">Tipe teks bebas tidak bisa dikaitkan ke unsur baku (jawabannya bukan angka).</div>
                </div>

                <div id="blok-unsur" class="mb-3">
                    <label class="form-label">Kaitkan ke Unsur (opsional)</label>
                    <select name="unsur_pelayanan_id" class="form-select">
                        <option value="">Tidak ada — pertanyaan tambahan (di luar 9 unsur wajib)</option>
                        @foreach ($daftarUnsur as $unsur)
                            <option value="{{ $unsur->id }}" @selected(old('unsur_pelayanan_id', $pertanyaan->unsur_pelayanan_id) == $unsur->id)>
                                {{ $unsur->kode }} - {{ $unsur->nama_unsur }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="blok-skala" class="mb-3">
                    <label class="form-label">Tampilkan Sebagai</label>
                    <select name="gaya_tampilan" class="form-select">
                        <option value="radio" @selected(old('gaya_tampilan', $pertanyaan->gaya_tampilan) === 'radio')>Tombol pilihan (radio)</option>
                        <option value="dropdown" @selected(old('gaya_tampilan', $pertanyaan->gaya_tampilan) === 'dropdown')>Dropdown</option>
                    </select>

                    <label class="form-label mt-3">Preset Label Skala (opsional)</label>
                    <select id="preset_label" class="form-select mb-2">
                        <option value="">-- Pilih preset atau isi manual di bawah --</option>
                        @foreach ($presetLabel as $key => $preset)
                            <option value="{{ $key }}" data-label='@json($preset['label'])'>{{ $preset['nama'] }}</option>
                        @endforeach
                    </select>

                    <div class="row g-2">
                        <div class="col-3">
                            <label class="form-label small">Level 1</label>
                            <input type="text" name="label_skala_1" class="form-control form-control-sm" value="{{ old('label_skala_1', $pertanyaan->label_skala_1) }}" placeholder="1">
                        </div>
                        <div class="col-3">
                            <label class="form-label small">Level 2</label>
                            <input type="text" name="label_skala_2" class="form-control form-control-sm" value="{{ old('label_skala_2', $pertanyaan->label_skala_2) }}" placeholder="2">
                        </div>
                        <div class="col-3">
                            <label class="form-label small">Level 3</label>
                            <input type="text" name="label_skala_3" class="form-control form-control-sm" value="{{ old('label_skala_3', $pertanyaan->label_skala_3) }}" placeholder="3">
                        </div>
                        <div class="col-3">
                            <label class="form-label small">Level 4</label>
                            <input type="text" name="label_skala_4" class="form-control form-control-sm" value="{{ old('label_skala_4', $pertanyaan->label_skala_4) }}" placeholder="4">
                        </div>
                    </div>
                    <div class="form-text">Dikosongkan semua = tampil sebagai angka 1, 2, 3, 4 biasa.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $pertanyaan->urutan) }}" min="1" required style="max-width:120px;">
                </div>
                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($pertanyaan->is_active)>
                    <label class="form-check-label fw-semibold" for="is_active">Aktif (ditampilkan di form survei)</label>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary fw-medium rounded-3 px-4">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('puskesmas.pertanyaan.index') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const tipeInput = document.getElementById('tipe_input');
            const blokUnsur = document.getElementById('blok-unsur');
            const blokSkala = document.getElementById('blok-skala');
            const presetLabel = document.getElementById('preset_label');
            const inputLabel = document.querySelectorAll('#blok-skala input[type=text]');

            function toggleTipe() {
                const isTeks = tipeInput.value === 'teks';
                blokUnsur.style.display = isTeks ? 'none' : '';
                blokSkala.style.display = isTeks ? 'none' : '';
            }

            tipeInput.addEventListener('change', toggleTipe);
            toggleTipe();

            presetLabel.addEventListener('change', function () {
                if (!this.value) return;
                const label = JSON.parse(this.selectedOptions[0].dataset.label);
                inputLabel.forEach((input, i) => { input.value = label[i]; });
            });
        })();
    </script>
@endsection
