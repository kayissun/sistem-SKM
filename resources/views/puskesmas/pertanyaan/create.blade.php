@extends('layouts.puskesmas')

@section('title', 'Tambah Pertanyaan Survei')

@section('content')
    <h3 class="mb-3">Tambah Pertanyaan Survei</h3>

    <form method="POST" action="{{ route('puskesmas.pertanyaan.store') }}" class="bg-white p-4 rounded border" style="max-width:650px" id="form-pertanyaan">
        @csrf

        <div class="mb-3">
            <label class="form-label">Teks pertanyaan</label>
            <input type="text" name="teks_pertanyaan" class="form-control" value="{{ old('teks_pertanyaan') }}" required
                   placeholder="contoh: Bagaimana kesopanan dan keramahan petugas dalam memberi pelayanan?">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipe jawaban</label>
            <select name="tipe_input" id="tipe_input" class="form-select">
                <option value="skala" @selected(old('tipe_input', 'skala') === 'skala')>Skala penilaian (1-4)</option>
                <option value="teks" @selected(old('tipe_input') === 'teks')>Teks bebas / masukan tertulis</option>
            </select>
            <div class="form-text">Tipe teks bebas tidak bisa dikaitkan ke unsur baku (jawabannya bukan angka).</div>
        </div>

        <div id="blok-unsur" class="mb-3">
            <label class="form-label">Kaitkan ke unsur (opsional)</label>
            <select name="unsur_pelayanan_id" class="form-select">
                <option value="">Tidak ada — pertanyaan tambahan (di luar 9 unsur wajib)</option>
                @foreach ($daftarUnsur as $unsur)
                    <option value="{{ $unsur->id }}" @selected(old('unsur_pelayanan_id') == $unsur->id)>
                        {{ $unsur->kode }} - {{ $unsur->nama_unsur }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">
                Kalau dikaitkan ke salah satu unsur, jawabannya ikut dihitung ke nilai SKM resmi.
            </div>
        </div>

        <div id="blok-skala" class="mb-3">
            <label class="form-label">Tampilkan sebagai</label>
            <select name="gaya_tampilan" class="form-select">
                <option value="radio" @selected(old('gaya_tampilan', 'radio') === 'radio')>Tombol pilihan (radio)</option>
                <option value="dropdown" @selected(old('gaya_tampilan') === 'dropdown')>Dropdown</option>
            </select>

            <label class="form-label mt-3">Preset label skala (opsional, tinggal pilih lalu edit kalau perlu)</label>
            <select id="preset_label" class="form-select mb-2">
                <option value="">-- Pilih preset atau isi manual di bawah --</option>
                @foreach ($presetLabel as $key => $preset)
                    <option value="{{ $key }}" data-label='@json($preset['label'])'>{{ $preset['nama'] }}</option>
                @endforeach
            </select>

            <div class="row g-2">
                <div class="col-3">
                    <label class="form-label small">Level 1</label>
                    <input type="text" name="label_skala_1" class="form-control form-control-sm" value="{{ old('label_skala_1') }}" placeholder="1">
                </div>
                <div class="col-3">
                    <label class="form-label small">Level 2</label>
                    <input type="text" name="label_skala_2" class="form-control form-control-sm" value="{{ old('label_skala_2') }}" placeholder="2">
                </div>
                <div class="col-3">
                    <label class="form-label small">Level 3</label>
                    <input type="text" name="label_skala_3" class="form-control form-control-sm" value="{{ old('label_skala_3') }}" placeholder="3">
                </div>
                <div class="col-3">
                    <label class="form-label small">Level 4</label>
                    <input type="text" name="label_skala_4" class="form-control form-control-sm" value="{{ old('label_skala_4') }}" placeholder="4">
                </div>
            </div>
            <div class="form-text">Dikosongkan semua = tampil sebagai angka 1, 2, 3, 4 biasa.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Urutan tampil</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $urutanBerikutnya) }}" min="1" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('puskesmas.pertanyaan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>

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
