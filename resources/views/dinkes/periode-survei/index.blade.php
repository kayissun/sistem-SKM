@extends('layouts.dinkes')

@section('title', 'Periode Survei')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Periode Survei</h3>
        <a href="{{ route('dinkes.periode-survei.create') }}" class="btn btn-primary btn-sm">+ Tambah periode</a>
    </div>

    <form method="POST" action="{{ route('dinkes.periode-survei.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="d-flex align-items-center gap-2 mb-2" id="bar-aksi-massal" style="display:none">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> periode dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-warning"
                    onclick="return confirm('Nonaktifkan semua periode yang dipilih?')">
                Nonaktifkan Terpilih
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN periode yang dipilih? Periode yang sudah punya data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                Hapus Terpilih
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                        <th>Nama periode</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th style="width:100px">Status</th>
                        <th style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPeriode as $periode)
                        <tr>
                            <td><input type="checkbox" name="dipilih[]" value="{{ $periode->id }}" class="cek-item"></td>
                            <td>{{ $periode->nama }}</td>
                            <td>{{ $periode->tanggal_mulai->format('d M Y') }}</td>
                            <td>{{ $periode->tanggal_selesai->format('d M Y') }}</td>
                            <td>
                                @if ($periode->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dinkes.periode-survei.edit', $periode) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('dinkes.periode-survei.destroy', $periode) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus periode ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    {{ $daftarPeriode->links() }}

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
