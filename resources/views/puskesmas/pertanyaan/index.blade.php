@extends('layouts.puskesmas')

@section('title', 'Pertanyaan Survei')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Pertanyaan Survei</h3>
        <a href="{{ route('puskesmas.pertanyaan.create') }}" class="btn btn-primary btn-sm">+ Tambah pertanyaan</a>
    </div>

    @if ($unsurBelumAda->isNotEmpty())
        <div class="alert alert-warning">
            <strong>Perhatian:</strong> unit ini belum punya pertanyaan aktif untuk unsur berikut,
            nilai SKM resmi akan kurang akurat sampai ditambahkan:
            <ul class="mb-0 mt-1">
                @foreach ($unsurBelumAda as $unsur)
                    <li>{{ $unsur->kode }} - {{ $unsur->nama_unsur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('puskesmas.pertanyaan.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="d-flex align-items-center gap-2 mb-2" id="bar-aksi-massal" style="display:none">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> pertanyaan dipilih</span>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN pertanyaan yang dipilih? Pertanyaan yang sudah punya jawaban responden tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                Hapus Terpilih
            </button>
        </div>
    </form>

    <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                    <th style="width:60px">Urutan</th>
                    <th>Pertanyaan</th>
                    <th style="width:140px">Tipe</th>
                    <th style="width:160px">Terkait unsur</th>
                    <th style="width:100px">Status</th>
                    <th style="width:160px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPertanyaan as $pertanyaan)
                    <tr>
                        <td><input type="checkbox" name="dipilih[]" value="{{ $pertanyaan->id }}" class="cek-item"></td>
                        <td>{{ $pertanyaan->urutan }}</td>
                        <td>{{ $pertanyaan->teks_pertanyaan }}</td>
                        <td>
                            @if ($pertanyaan->tipe_input === 'teks')
                                <span class="badge bg-info text-dark">Teks bebas</span>
                            @else
                                <span class="badge bg-light text-dark border">Skala · {{ $pertanyaan->gaya_tampilan === 'dropdown' ? 'Dropdown' : 'Radio' }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($pertanyaan->unsurPelayanan)
                                <span class="badge bg-primary">{{ $pertanyaan->unsurPelayanan->kode }}</span>
                            @else
                                <span class="badge bg-secondary">Tambahan</span>
                            @endif
                        </td>
                        <td>
                            @if ($pertanyaan->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('puskesmas.pertanyaan.edit', $pertanyaan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('puskesmas.pertanyaan.destroy', $pertanyaan) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus pertanyaan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Belum ada pertanyaan. Tambahkan pertanyaan pertama Anda lewat tombol di atas.</td></tr>
                @endforelse
            </tbody>
    </table>

    <script>
        (function () {
            const pilihSemua = document.getElementById('pilih-semua');
            const bar = document.getElementById('bar-aksi-massal');
            const jumlahEl = document.getElementById('jumlah-terpilih');

            function daftarItem() {
                return document.querySelectorAll('.cek-item');
            }

            function refresh() {
                const dicek = document.querySelectorAll('.cek-item:checked').length;
                jumlahEl.textContent = dicek;
                bar.style.display = dicek > 0 ? 'flex' : 'none';
            }

            if (pilihSemua) {
                pilihSemua.addEventListener('change', function () {
                    daftarItem().forEach(cb => { cb.checked = pilihSemua.checked; });
                    refresh();
                });
            }

            daftarItem().forEach(cb => cb.addEventListener('change', refresh));

            document.getElementById('form-aksi-massal').addEventListener('submit', function (event) {
                const terpilih = document.querySelectorAll('.cek-item:checked');

                if (terpilih.length === 0) {
                    event.preventDefault();
                    alert('Pilih minimal satu pertanyaan untuk dihapus.');
                    return;
                }

                terpilih.forEach(function (checkbox) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'dipilih[]';
                    input.value = checkbox.value;
                    this.appendChild(input);
                }, this);
            });
        })();
    </script>
@endsection
