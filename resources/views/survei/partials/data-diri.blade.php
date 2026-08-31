<h5 tabindex="-1" class="mb-1">Data diri</h5>
<p class="skm-hint mb-3">Kolom bertanda <span class="skm-required">*</span> wajib diisi.</p>

<label class="skm-field-label" for="dd_nama">Nama <span class="skm-required">*</span></label>
<input id="dd_nama" class="form-control mb-2" name="nama" value="{{ old('nama') }}" required>

<label class="skm-field-label" for="dd_no_hp">No. WA/HP <span class="skm-required">*</span></label>
<input id="dd_no_hp" class="form-control mb-2" name="no_hp" value="{{ old('no_hp') }}"
       inputmode="numeric" pattern="[0-9]*" maxlength="15"
       placeholder="contoh: 081234567890 (angka saja)" required>

<label class="skm-field-label" for="dd_unit">Unit layanan yang dikunjungi <span class="skm-required">*</span></label>
@if ($daftarUnitLayanan->isNotEmpty())
    <select id="dd_unit" class="form-select mb-2" name="unit_layanan_id" required>
        <option value="">-- Pilih --</option>
        @foreach ($daftarUnitLayanan as $unit)
            <option value="{{ $unit->id }}" @selected(old('unit_layanan_id') == $unit->id)>{{ $unit->nama }}</option>
        @endforeach
    </select>
@else
    <input id="dd_unit" class="form-control mb-2" disabled placeholder="Belum ada unit layanan terdaftar">
@endif

<label class="skm-field-label" for="dd_gender">Jenis kelamin</label>
<select id="dd_gender" class="form-select mb-2" name="jenis_kelamin">
    <option value="">Tidak menjawab</option>
    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
</select>

<label class="skm-field-label" for="dd_umur">Umur <span class="skm-required">*</span></label>
<input id="dd_umur" class="form-control mb-2" name="umur" value="{{ old('umur') }}"
       type="number" inputmode="numeric" min="0" max="120" placeholder="contoh: 28" required>

<label class="skm-field-label" for="dd_pendidikan">Pendidikan terakhir <span class="skm-required">*</span></label>
<select id="dd_pendidikan" class="form-select mb-2" name="pendidikan" required>
    <option value="">-- Pilih --</option>
    @foreach ($opsiPendidikan as $opsi)
        <option value="{{ $opsi }}" @selected(old('pendidikan') === $opsi)>{{ $opsi }}</option>
    @endforeach
</select>

<label class="skm-field-label" for="dd_pekerjaan">Pekerjaan <span class="skm-required">*</span></label>
<select id="dd_pekerjaan" class="form-select mb-3" name="pekerjaan" required>
    <option value="">-- Pilih --</option>
    @foreach ($opsiPekerjaan as $opsi)
        <option value="{{ $opsi }}" @selected(old('pekerjaan') === $opsi)>{{ $opsi }}</option>
    @endforeach
</select>