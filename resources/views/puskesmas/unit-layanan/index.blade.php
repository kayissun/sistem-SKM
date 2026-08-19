@extends('layouts.puskesmas')

@section('title', 'Unit Layanan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Unit Layanan / Poli</h3>
        <a href="{{ route('puskesmas.unit-layanan.create') }}" class="btn btn-primary btn-sm">+ Tambah unit layanan</a>
    </div>
    <p class="text-muted">Daftar ini akan muncul sebagai pilihan dropdown di form survei publik.</p>

    <form method="POST" action="{{ route('puskesmas.unit-layanan.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="d-flex align-items-center gap-2 mb-2" id="bar-aksi-massal" style="display:none">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unit dipilih</span>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN unit layanan yang dipilih? Unit yang sudah dipakai di data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                Hapus Terpilih
            </button>
        </div>
    </form>

    <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                    <th>Nama</th>
                    <th style="width:100px">Status</th>
                    <th style="width:160px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarUnitLayanan as $unit)
                    <tr>
                        <td><input type="checkbox" name="dipilih[]" value="{{ $unit->id }}" class="cek-item"></td>
                        <td>{{ $unit->nama }}</td>
                        <td>
                            @if ($unit->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('puskesmas.unit-layanan.edit', $unit) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('puskesmas.unit-layanan.destroy', $unit) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus unit layanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Belum ada unit layanan</td></tr>
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
                    alert('Pilih minimal satu unit layanan untuk dihapus.');
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
