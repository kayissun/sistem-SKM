@extends('layouts.puskesmas')

@section('title', 'Edit Pertanyaan Survei')

@section('content')
    <h3 class="mb-3">Edit Pertanyaan</h3>

    <form method="POST" action="{{ route('puskesmas.pertanyaan.update', $pertanyaan) }}" class="bg-white p-4 rounded border" style="max-width:600px">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Kaitkan ke unsur (opsional)</label>
            <select name="unsur_pelayanan_id" class="form-select">
                <option value="">Tidak ada — pertanyaan tambahan (di luar 9 unsur wajib)</option>
                @foreach ($daftarUnsur as $unsur)
                    <option value="{{ $unsur->id }}" @selected(old('unsur_pelayanan_id', $pertanyaan->unsur_pelayanan_id) == $unsur->id)>
                        {{ $unsur->kode }} - {{ $unsur->pertanyaan }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Teks pertanyaan</label>
            <input type="text" name="teks_pertanyaan" class="form-control" value="{{ old('teks_pertanyaan', $pertanyaan->teks_pertanyaan) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Urutan tampil</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $pertanyaan->urutan) }}" min="1" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked($pertanyaan->is_active)>
            <label class="form-check-label" for="is_active">Aktif (ditampilkan di form survei)</label>
        </div>

        <button class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('puskesmas.pertanyaan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
@endsection
