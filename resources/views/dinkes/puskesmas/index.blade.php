@extends('layouts.dinkes')

@section('title', 'Puskesmas / RSU')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Puskesmas / RSU</h3>
        <a href="{{ route('dinkes.puskesmas.create') }}" class="btn btn-primary btn-sm">+ Tambah unit</a>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label small mb-1">Cari</label>
            <input type="text" name="cari" value="{{ $pencarian }}" class="form-control form-control-sm"
                   placeholder="Cari nama, alamat, atau kecamatan...">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Urutkan nama</label>
            <select name="urutan" class="form-select form-select-sm">
                <option value="az" @selected($urutan === 'az')>A - Z</option>
                <option value="za" @selected($urutan === 'za')>Z - A</option>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
            <a href="{{ route('dinkes.puskesmas.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>

    <form method="POST" action="{{ route('dinkes.puskesmas.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="d-flex align-items-center gap-2 mb-2" id="bar-aksi-massal" style="display:none">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unit dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-warning"
                    onclick="return confirm('Nonaktifkan semua unit yang dipilih?')">
                Nonaktifkan Terpilih
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN unit yang dipilih? Unit yang sudah punya data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                Hapus Terpilih
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered bg-white align-middle">
                <thead>
                    <tr>
                        <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Alamat</th>
                        <th>Kecamatan</th>
                        <th>No. Telepon</th>
                        <th>Jumlah akun</th>
                        <th>Email Admin</th>
                        <th>Status</th>
                        <th style="width:230px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPuskesmas as $item)
                        <tr>
                            <td><input type="checkbox" name="dipilih[]" value="{{ $item->id }}" class="cek-item"></td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ strtoupper($item->jenis) }}</td>
                            <td>{{ $item->alamat ?? '-' }}</td>
                            <td>{{ $item->kecamatan ?? '-' }}</td>
                            <td>{{ $item->no_telepon ?? '-' }}</td>
                            <td>{{ $item->users_count }}</td>
                            <td>
                                @php $admin = $item->users->first(fn ($u) => $u->hasRole('admin-puskesmas')); @endphp
                                {{ $admin->email ?? '-' }}
                            </td>
                            <td>
                                @if ($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dinkes.puskesmas.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="{{ route('survei.create', $item) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Link</a>
                                @if ($item->is_active)
                                    <a href="{{ route('qrcode.unduh', $item) }}" class="btn btn-sm btn-outline-secondary">QR</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                @if ($pencarian)
                                    Tidak ada unit yang cocok dengan pencarian "{{ $pencarian }}".
                                @else
                                    Belum ada data
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    {{ $daftarPuskesmas->links() }}

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
        })();
    </script>
@endsection
