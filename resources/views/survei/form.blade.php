@extends('layouts.publik')

@section('title', 'Survei Kepuasan - ' . $puskesmas->nama)

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h4 class="mb-1">Survei Kepuasan Masyarakat</h4>
            <p class="text-muted mb-4">{{ $puskesmas->nama }}</p>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (!$periodeAktif)
                <div class="alert alert-warning">
                    Survei sedang tidak dibuka saat ini. Silakan hubungi petugas {{ $puskesmas->nama }}.
                </div>
            @elseif ($daftarPertanyaan->isEmpty())
                <div class="alert alert-warning">
                    Kuesioner belum tersedia. Silakan coba lagi nanti.
                </div>
            @else
                <form method="POST" action="{{ route('survei.store', $puskesmas) }}">
                    @csrf

                    <h6 class="mt-2 mb-3">Data diri <span class="text-danger">(wajib diisi)</span></h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. WA/HP <span class="text-danger">*</span></label>
                            <input type="tel" name="no_hp" class="form-control" value="{{ old('no_hp') }}" required
                                   inputmode="numeric" pattern="[0-9]*" maxlength="15"
                                   placeholder="contoh: 081234567890 (angka saja)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit layanan yang dikunjungi <span class="text-danger">*</span></label>
                            @if ($daftarUnitLayanan->isNotEmpty())
                                <select name="unit_layanan_id" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($daftarUnitLayanan as $unit)
                                        <option value="{{ $unit->id }}" @selected(old('unit_layanan_id') == $unit->id)>
                                            {{ $unit->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control" disabled placeholder="Belum ada unit layanan terdaftar">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">Tidak menjawab</option>
                                <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Umur <span class="text-danger">*</span></label>
                            <input type="number" name="umur" class="form-control" value="{{ old('umur') }}" required
                                   inputmode="numeric" min="0" max="120" placeholder="contoh: 28">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pendidikan terakhir <span class="text-danger">*</span></label>
                            <select name="pendidikan" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsiPendidikan as $opsi)
                                    <option value="{{ $opsi }}" @selected(old('pendidikan') === $opsi)>{{ $opsi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                            <select name="pekerjaan" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsiPekerjaan as $opsi)
                                    <option value="{{ $opsi }}" @selected(old('pekerjaan') === $opsi)>{{ $opsi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="mb-2">Penilaian layanan</h6>
                    <p class="text-muted small">Skala 1 = sangat tidak baik, 4 = sangat baik.</p>

                    @foreach ($daftarPertanyaan as $pertanyaan)
                        <div class="mb-3 pb-3 border-bottom">
                            <label class="form-label">{{ $loop->iteration }}. {{ $pertanyaan->teks_pertanyaan }}</label>

                            @if ($pertanyaan->tipe_input === 'teks')
                                <textarea name="jawaban[{{ $pertanyaan->id }}]" class="form-control" rows="3"
                                          placeholder="Tulis masukan Anda di sini (opsional)">{{ old('jawaban.' . $pertanyaan->id) }}</textarea>
                            @elseif ($pertanyaan->gaya_tampilan === 'dropdown')
                                <select name="jawaban[{{ $pertanyaan->id }}]" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($pertanyaan->labelSkala() as $nilai => $label)
                                        <option value="{{ $nilai }}" @selected(old('jawaban.' . $pertanyaan->id) == $nilai)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="btn-group-vertical w-100" role="group">
                                    @foreach ($pertanyaan->labelSkala() as $nilai => $label)
                                        <input type="radio" class="btn-check" name="jawaban[{{ $pertanyaan->id }}]"
                                               id="p{{ $pertanyaan->id }}_{{ $nilai }}" value="{{ $nilai }}"
                                               @checked(old('jawaban.' . $pertanyaan->id) == $nilai) required>
                                        <label class="btn btn-outline-primary text-start" for="p{{ $pertanyaan->id }}_{{ $nilai }}">
                                            {{ $nilai }} — {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary w-100 mt-3">Kirim penilaian</button>
                </form>
            @endif
        </div>
    </div>
@endsection
