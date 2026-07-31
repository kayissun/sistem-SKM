@extends('layouts.puskesmas')

@section('title', 'Tambah Pertanyaan Survei')

@section('content')
    <h3 class="mb-3">Tambah Pertanyaan Survei</h3>

    <form method="POST" action="{{ route('puskesmas.pertanyaan.store') }}" class="bg-white p-4 rounded border" style="max-width:600px">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kaitkan ke unsur (opsional)</label>
            <select name="unsur_pelayanan_id" class="form-select">
                <option value="">Tidak ada — pertanyaan tambahan (di luar 9 unsur wajib)</option>
                @foreach ($daftarUnsur as $unsur)
                    <option value="{{ $unsur->id }}" @selected(old('unsur_pelayanan_id') == $unsur->id)>
                        {{ $unsur->kode }} - {{ $unsur->pertanyaan }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">
                Kalau dikaitkan ke salah satu unsur, jawabannya ikut dihitung ke nilai SKM resmi.
                Kalau dibiarkan kosong, pertanyaan cuma jadi info tambahan, tidak memengaruhi nilai SKM.
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Teks pertanyaan</label>
            <input type="text" name="teks_pertanyaan" class="form-control" value="{{ old('teks_pertanyaan') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Urutan tampil</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $urutanBerikutnya) }}" min="1" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('puskesmas.pertanyaan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
