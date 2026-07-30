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
            @elseif ($unsurAktif->isEmpty())
                <div class="alert alert-warning">
                    Kuesioner belum tersedia. Silakan coba lagi nanti.
                </div>
            @else
                <form method="POST" action="{{ route('survei.store', $puskesmas) }}">
                    @csrf

                    <h6 class="mt-2 mb-3">Data diri (opsional)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Unit layanan yang dikunjungi</label>
                            <input type="text" name="unit_layanan" class="form-control" value="{{ old('unit_layanan') }}" placeholder="contoh: Poli Umum">
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
                            <label class="form-label">Rentang usia</label>
                            <select name="usia_rentang" class="form-select">
                                <option value="">Tidak menjawab</option>
                                <option value="17-30" @selected(old('usia_rentang') === '17-30')>17-30 tahun</option>
                                <option value="31-45" @selected(old('usia_rentang') === '31-45')>31-45 tahun</option>
                                <option value="46-60" @selected(old('usia_rentang') === '46-60')>46-60 tahun</option>
                                <option value=">60" @selected(old('usia_rentang') === '>60')>Di atas 60 tahun</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pendidikan terakhir</label>
                            <input type="text" name="pendidikan" class="form-control" value="{{ old('pendidikan') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
                        </div>
                    </div>

                    <h6 class="mb-2">Penilaian layanan</h6>
                    <p class="text-muted small">Skala 1 = sangat tidak baik, 4 = sangat baik.</p>

                    @foreach ($unsurAktif as $unsur)
                        <div class="mb-3 pb-3 border-bottom">
                            <label class="form-label">{{ $loop->iteration }}. {{ $unsur->pertanyaan }}</label>
                            <div class="btn-group w-100" role="group">
                                @foreach ([1, 2, 3, 4] as $skala)
                                    <input type="radio" class="btn-check" name="jawaban[{{ $unsur->id }}]"
                                           id="u{{ $unsur->id }}_{{ $skala }}" value="{{ $skala }}"
                                           @checked(old('jawaban.' . $unsur->id) == $skala) required>
                                    <label class="btn btn-outline-primary" for="u{{ $unsur->id }}_{{ $skala }}">{{ $skala }}</label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary w-100 mt-3">Kirim penilaian</button>
                </form>
            @endif
        </div>
    </div>
@endsection
