@extends('layouts.dinkes')

@section('title', 'Laporan Rekap')

@section('content')

    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: #180733; margin: 0; }
        .sp-page-head p { margin: 2px 0 0; color: #635C7A; font-size: .88rem; }

        .sp-filter-card { border-radius: 14px; }
        .sp-filter-card .input-group-text { background: #FAF8FF; border-right: none; color: #9CA3AF; }
        .sp-filter-card .form-control, .sp-filter-card .form-select { border-left: none; background: #FAF8FF; }
        .sp-filter-card .input-group:focus-within .input-group-text,
        .sp-filter-card .input-group:focus-within .form-control { border-color: #A78BFA; }

        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table-responsive { scrollbar-width: thin; scrollbar-color: #C4B5FD transparent; }
        .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: #FAF8FF; }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .6rem .85rem; white-space: nowrap; }
        .sp-table-card thead th { font-size: .72rem; }

        .badge-mutu { font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .75rem; }
        .badge-mutu-a { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .badge-mutu-b { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
        .badge-mutu-c { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
        .badge-mutu-d { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }

        .sp-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid rgba(109,40,217,.15);
            background: #fff; color: #6D28D9; transition: .15s;
        }
        .sp-icon-btn:hover { background: #6D28D9; color: #fff; border-color: #6D28D9; }

        .sp-bulkbar {
            display: none; align-items: center; gap: 12px;
            background: #FAF8FF; border: 1px solid #E4DEF7; border-radius: 12px;
            padding: 10px 16px; margin-bottom: 14px;
        }
    </style>

    <!-- Header -->
    <div class="sp-page-head">
        <div>
            <h3>Laporan Rekap SKM</h3>
            <p>Rekapitulasi nilai Indeks Kepuasan Masyarakat dari seluruh unit pelayanan.</p>
        </div>
        @if ($periode)
            <div class="d-flex gap-2">
                <a href="{{ route('dinkes.laporan.export-pdf', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm rounded-3">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('dinkes.laporan.export-excel', ['periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm rounded-3">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                </a>
            </div>
        @endif
    </div>

    <!-- Filter & Pencarian -->
    <div class="card sp-filter-card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <!-- Filter Periode Survei -->
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-calendar-days me-1"></i> Periode Survei
                    </label>
                    <select name="periode_survei_id" class="form-select border rounded-3" onchange="this.form.submit()">
                        @foreach ($daftarPeriode as $p)
                            <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                                {{ $p->nama }} @if($p->is_active) (Aktif) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pencarian Unit + Tombol Filter & Reset -->
                <div class="col-md-7">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Pencarian Unit
                    </label>
                    <div class="input-group">
                        <input type="text" name="cari" value="{{ $pencarian ?? '' }}" class="form-control border rounded-start-3" placeholder="Cari nama unit pelayanan...">
                        <button class="btn btn-primary fw-medium" type="submit" title="Cari / Filter">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                        </button>
                        <a href="{{ route('dinkes.laporan.index') }}" class="btn btn-outline-secondary fw-medium rounded-end-3" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (!$periode)
        <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 text-center">
            <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 text-warning"></i>
            <h6 class="fw-bold mb-1">Belum Ada Periode Survei</h6>
            <p class="mb-0 small text-muted">Silakan buat periode survei aktif terlebih dahulu untuk melihat rekapitulasi laporan.</p>
        </div>
    @else
        @php $kodeUnsur = $rekap->isNotEmpty() ? array_keys($rekap->first()['per_unsur']) : []; @endphp

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="small text-muted fw-semibold">Total {{ count($rekap) }} unit pelayanan terkap</span>
            <button type="button" class="btn btn-sm btn-light border text-purple font-medium rounded-3" onclick="salinTabelKeClipboard('tabel-rekap-gabungan', this)">
                <i class="fa-solid fa-copy me-1"></i> Salin Tabel
            </button>
        </div>

        <form method="POST" action="{{ route('dinkes.puskesmas.aksi-massal') }}" id="form-aksi-massal">
            @csrf

            <div class="sp-bulkbar" id="bar-aksi-massal">
                <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unit dipilih</span>
                <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-primary" onclick="return confirm('Nonaktifkan semua unit yang dipilih?')">
                    <i class="fa-solid fa-pause me-1"></i> Nonaktifkan Terpilih
                </button>
                <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus PERMANEN unit yang dipilih?')">
                    <i class="fa-solid fa-trash me-1"></i> Hapus Terpilih
                </button>
            </div>

            <div class="card sp-table-card border-0 shadow-sm">
                <div class="table-responsive">
                    <table id="tabel-rekap-gabungan" class="table text-center align-middle mb-0">
                        <thead class="bg-light text-uppercase text-muted">
                            <tr>
                                <th rowspan="2" class="align-middle" style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                                <th rowspan="2" class="align-middle" style="width:40px">No</th>
                                <th rowspan="2" class="align-middle text-start">OPD / Unit Pelayanan Publik</th>
                                <th rowspan="2" class="align-middle">Periode</th>
                                <th colspan="{{ count($kodeUnsur) }}" class="bg-purple-subtle text-purple border-bottom">Nilai Per Unsur</th>
                                <th rowspan="2" class="align-middle">IKM</th>
                                <th rowspan="2" class="align-middle">Kategori</th>
                                <th rowspan="2" class="align-middle">Responden</th>
                                <th rowspan="2" class="align-middle">Metode</th>
                                <th rowspan="2" class="align-middle">Prioritas Perbaikan</th>
                                <th rowspan="2" class="align-middle">Rencana Tindak Lanjut</th>
                                <th rowspan="2" class="align-middle">Detail</th>
                            </tr>
                            <tr>
                                @foreach ($kodeUnsur as $kode)
                                    <th class="bg-purple-subtle text-purple fs-8">{{ $kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekap as $i => $baris)
                                <tr>
                                    <td><input type="checkbox" name="dipilih[]" value="{{ $baris['puskesmas_id'] }}" class="cek-item"></td>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-start fw-semibold" style="color:#180733">{{ $baris['puskesmas'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $namaPeriodeLengkap ?? $periode?->nama }}</span></td>
                                    @foreach ($kodeUnsur as $kode)
                                        <td class="text-secondary">{{ number_format($baris['per_unsur'][$kode]['nrr_skala_100'] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="fw-bold text-purple">{{ $baris['nilai_akhir_skm'] }}</td>
                                    <td>
                                        @php
                                            $mutu = strtolower($baris['mutu_akhir']);
                                            $badgeClass = str_contains($mutu, 'sangat baik') || str_contains($mutu, 'a') ? 'badge-mutu-a' :
                                                          (str_contains($mutu, 'baik') || str_contains($mutu, 'b') ? 'badge-mutu-b' :
                                                          (str_contains($mutu, 'kurang') || str_contains($mutu, 'c') ? 'badge-mutu-c' : 'badge-mutu-d'));
                                        @endphp
                                        <span class="badge-mutu {{ $badgeClass }}">{{ $baris['mutu_akhir'] }}</span>
                                    </td>
                                    <td class="fw-medium">{{ number_format($baris['jumlah_responden']) }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary border">Online</span></td>
                                    <td class="text-start text-muted">
                                        @foreach ($baris['unsur_prioritas'] as $prioritas)
                                            <div>{{ $prioritas }}</div>
                                        @endforeach
                                    </td>
                                    <td class="text-start text-muted">
                                        @foreach ($baris['rencana_tindak_lanjut'] as $rencana)
                                            <div>{{ $rencana }}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $baris['puskesmas_id'], 'periode_survei_id' => $periode->id]) }}"
                                           class="sp-icon-btn" title="Lihat Detail">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 12 + count($kodeUnsur) }}" class="text-center text-muted py-5">
                                        <i class="fa-regular fa-folder-open fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                        Belum ada data unit pelayanan aktif pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    @endif

    <script>
        (function () {
            const pilihSemua = document.getElementById('pilih-semua');
            const bar = document.getElementById('bar-aksi-massal');
            const jumlahEl = document.getElementById('jumlah-terpilih');

            function daftarItem() { return document.querySelectorAll('.cek-item'); }

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

            document.getElementById('form-aksi-massal')?.addEventListener('submit', function (event) {
                if (document.querySelectorAll('.cek-item:checked').length === 0) {
                    event.preventDefault();
                    alert('Pilih minimal satu unit untuk diproses.');
                }
            });
        })();
    </script>
@endsection