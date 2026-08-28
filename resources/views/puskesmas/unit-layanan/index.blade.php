@extends('layouts.puskesmas')

@section('title', 'Unit Layanan')

@section('content')
    <div class="sp-page-head">
        <div>
            <h3>Unit Layanan / Poli</h3>
            <p>{{ $daftarUnitLayanan->count() }} unit terdaftar. Daftar ini muncul sebagai pilihan dropdown di form survei publik.</p>
        </div>
        <a href="{{ route('puskesmas.unit-layanan.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Tambah Unit Layanan
        </a>
    </div>

    <form method="POST" action="{{ route('puskesmas.unit-layanan.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unit dipilih</span>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN unit layanan yang dipilih? Unit yang sudah dipakai di data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus Terpilih
            </button>
        </div>

        <div class="card sp-table-card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                            <th>Nama</th>
                            <th style="width:120px">Status</th>
                            <th style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarUnitLayanan as $unit)
                            <tr>
                                <td><input type="checkbox" name="dipilih[]" value="{{ $unit->id }}" class="cek-item"></td>
                                <td class="fw-semibold" style="color:#180733">{{ $unit->nama }}</td>
                                <td>
                                    @if ($unit->is_active)
                                        <span class="badge-status-active">Aktif</span>
                                    @else
                                        <span class="badge-status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('puskesmas.unit-layanan.edit', $unit) }}" class="sp-icon-btn" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('puskesmas.unit-layanan.destroy', $unit) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus unit layanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="sp-icon-btn" title="Hapus" style="color:#DC2626;border-color:rgba(220,38,38,.15)">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada unit layanan. Klik tombol <strong>Tambah Unit Layanan</strong> untuk menambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <div class="mt-3 sp-pagination">
        {{ $daftarUnitLayanan->links('pagination::bootstrap-5') }}
    </div>

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
