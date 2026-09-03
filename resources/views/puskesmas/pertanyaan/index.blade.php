@extends('layouts.puskesmas')

@section('title', 'Pertanyaan Survei')

@section('content')
    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: #180733; margin: 0; }
        .sp-page-head p { margin: 2px 0 0; color: #635C7A; font-size: .88rem; }

        .sp-table-card { border-radius: 14px; overflow: hidden; border: 1px solid #E4DEF7; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: #FAF8FF; }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .65rem .85rem; border-bottom: 1px solid #E4DEF7; border-right: 1px solid #E4DEF7; }
        .sp-table-card td:last-child, .sp-table-card th:last-child { border-right: none; }
        .sp-table-card thead th { font-size: .72rem; border-bottom: 2px solid #E4DEF7; background: #FAF8FF; }
        .sp-table-card tbody tr:last-child td { border-bottom: none; }

        .sp-bulkbar {
            display: none; align-items: center; gap: 12px;
            background: #FAF8FF; border: 1px solid #E4DEF7; border-radius: 12px;
            padding: 10px 16px; margin-bottom: 14px;
        }

        .sp-icon-btn {
            width: 30px; height: 30px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid rgba(109,40,217,.15);
            background: #fff; color: #6D28D9; transition: .15s; text-decoration: none;
        }
        .sp-icon-btn:hover { background: #6D28D9; color: #fff; border-color: #6D28D9; }
    </style>

    <!-- Header -->
    <div class="sp-page-head">
        <div>
            <h3>Pertanyaan Survei</h3>
            <p>Kelola daftar pertanyaan yang akan ditampilkan pada form survei kepuasan masyarakat.</p>
        </div>
        <a href="{{ route('puskesmas.pertanyaan.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Bangun Pertanyaan
        </a>
    </div>

    @if ($unsurBelumAda->isNotEmpty())
        <div class="alert alert-warning border-0 rounded-4 shadow-sm d-flex align-items-start gap-3 mb-4">
            <i class="fa-solid fa-triangle-exclamation text-warning fs-4 mt-1"></i>
            <div>
                <strong>Perhatian:</strong> Unit ini belum punya pertanyaan aktif untuk unsur berikut,
                nilai SKM resmi akan kurang akurat sampai ditambahkan:
                <ul class="mb-0 mt-1">
                    @foreach ($unsurBelumAda as $unsur)
                        <li class="small">{{ $unsur->kode }} - {{ $unsur->nama_unsur }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('puskesmas.pertanyaan.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> pertanyaan dipilih</span>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger fw-medium rounded-3"
                    onclick="return confirm('Hapus PERMANEN pertanyaan yang dipilih? Pertanyaan yang sudah punya jawaban responden tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus Terpilih
            </button>
        </div>

        <div class="card sp-table-card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                            <th style="width:56px">Urutan</th>
                            <th>Pertanyaan</th>
                            <th style="width:140px">Tipe</th>
                            <th style="width:140px">Terkait Unsur</th>
                            <th style="width:90px">Status</th>
                            <th style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPertanyaan as $pertanyaan)
                            <tr>
                                <td><input type="checkbox" name="dipilih[]" value="{{ $pertanyaan->id }}" class="cek-item"></td>
                                <td class="text-center fw-bold text-purple">{{ $pertanyaan->urutan }}</td>
                                <td class="fw-semibold" style="color:#180733">{{ $pertanyaan->teks_pertanyaan }}</td>
                                <td>
                                    @if ($pertanyaan->tipe_input === 'teks')
                                        <span class="sp-badge-chip-gold">Teks bebas</span>
                                    @else
                                        <span class="sp-badge-chip-light">Skala · {{ $pertanyaan->gaya_tampilan === 'dropdown' ? 'Dropdown' : 'Radio' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($pertanyaan->unsurPelayanan)
                                        <span class="sp-badge-chip-light">{{ $pertanyaan->unsurPelayanan->kode }}</span>
                                    @else
                                        <span class="sp-badge-chip-muted">Tambahan</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($pertanyaan->is_active)
                                        <span class="badge-status-active">Aktif</span>
                                    @else
                                        <span class="badge-status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('puskesmas.pertanyaan.edit', $pertanyaan) }}" class="sp-icon-btn" title="Edit">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </a>
                                        <form action="{{ route('puskesmas.pertanyaan.destroy', $pertanyaan) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus pertanyaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="sp-icon-btn" title="Hapus" style="color:#DC2626;">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa-regular fa-folder-open fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                    Belum ada pertanyaan. Tambahkan pertanyaan pertama lewat tombol di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

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
