@extends('layouts.dinkes')

@section('title', 'Periode Survei')

@section('content')

    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: var(--purple-900); margin: 0; }
        .sp-page-head p { margin: 4px 0 0; color: var(--ink-muted); font-size: .85rem; }

        .sp-stat-row { display: flex; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-stat-card {
            flex: 1; min-width: 140px;
            background: #fff; border: 1px solid rgba(24,7,51,.06);
            border-radius: 14px; padding: 16px 20px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: 0 1px 3px rgba(24,7,51,.04);
        }
        .sp-stat-card .icon {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff; flex-shrink: 0;
        }
        .sp-stat-card .label { font-size: .72rem; font-weight: 600; color: var(--ink-muted); text-transform: uppercase; letter-spacing: .04em; }
        .sp-stat-card .value { font-size: 1.35rem; font-weight: 800; color: var(--purple-900); line-height: 1.2; }

        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table-responsive { scrollbar-width: thin; scrollbar-color: #C4B5FD transparent; }
        .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-track { background: transparent; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr { transition: background .15s; }
        .sp-table-card tbody tr:hover { background: var(--surface-1); }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .65rem .9rem; white-space: nowrap; }
        .sp-table-card thead th { font-size: .7rem; }

        .badge-status-active   { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .76rem; }
        .badge-status-inactive { background: #F3F1FA; color: #6B6480; border: 1px solid #E4DEF7; font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .76rem; }
        .badge-status-selesai  { background: #FFF7ED; color: #9A3412; border: 1px solid #FED7AA; font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .76rem; }
        .badge-status-akan-datang { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .76rem; }

        .sp-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid rgba(109,40,217,.15);
            background: #fff; color: #6D28D9; text-decoration: none;
            transition: .15s;
        }
        .sp-icon-btn:hover { background: #6D28D9; color: #fff; border-color: #6D28D9; }
        .sp-icon-btn.success { color: #059669; border-color: rgba(5,150,105,.15); }
        .sp-icon-btn.success:hover { background: #059669; color: #fff; border-color: #059669; }
        .sp-icon-btn.danger { color: #DC2626; border-color: rgba(220,38,38,.15); }
        .sp-icon-btn.danger:hover { background: #DC2626; color: #fff; border-color: #DC2626; }

        .sp-bulkbar {
            display: none;
            align-items: center;
            gap: 12px;
            background: var(--surface-1);
            border: 1px solid #E4DEF7;
            border-radius: 12px;
            padding: 10px 16px;
            margin-bottom: 14px;
        }

        .sp-modal .modal-content { border: none; border-radius: 18px; overflow: hidden; }
        .sp-modal .modal-header {
            background: linear-gradient(135deg,#7C3AED,#2A0B5E);
            color: #fff;
            border: none;
            padding: 20px 26px;
        }
        .sp-modal .modal-header .modal-title { font-weight: 800; font-size: 1.05rem; }
        .sp-modal .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .sp-modal .modal-body { padding: 26px; }
        .sp-modal .modal-footer { border: none; padding: 16px 26px 24px; }
        .sp-section-label {
            font-size: .72rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
            color: #6D28D9; margin-bottom: 12px;
        }
        .form-switch .form-check-input:checked { background-color: #6D28D9; border-color: #6D28D9; }

        .sp-quarter-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
        .sp-quarter-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 10px;
            border: 1px solid rgba(109,40,217,.15);
            background: #fff; color: var(--purple-700);
            font-size: .8rem; font-weight: 600;
            cursor: pointer; transition: .15s;
            user-select: none;
        }
        .sp-quarter-chip:hover { background: var(--purple-100); border-color: var(--purple-700); }
        .sp-quarter-chip.active { background: var(--purple-700); color: #fff; border-color: var(--purple-700); }
        .sp-quarter-chip i { font-size: .7rem; }

        .sp-date-range {
            display: flex; align-items: center; gap: 8px;
            padding: 12px 16px; border-radius: 10px;
            background: var(--purple-50); border: 1px solid rgba(109,40,217,.1);
            margin-bottom: 16px;
        }
        .sp-date-range .label { font-size: .72rem; font-weight: 600; color: var(--ink-muted); text-transform: uppercase; letter-spacing: .03em; }
        .sp-date-range .value { font-size: .88rem; font-weight: 700; color: var(--purple-900); }
        .sp-date-range .separator { color: var(--purple-500); font-weight: 800; }
    </style>

    {{-- Page Header --}}
    <div class="sp-page-head">
        <div>
            <h3>Periode Survei</h3>
            <p>Kelola jadwal triwulan untuk survei kepuasan masyarakat.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPeriode"
                onclick="bukaModalTambah()">
            <i class="fa-solid fa-plus me-1"></i> Tambah Periode
        </button>
    </div>

    {{-- Stat Cards --}}
    <div class="sp-stat-row">
        <div class="sp-stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#7C3AED,#5B21B6)">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <div class="label">Total Periode</div>
                <div class="value">{{ $totalPeriode }}</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#10B981,#047857)">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="label">Aktif</div>
                <div class="value">{{ $aktif }}</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#3B82F6,#1D4ED8)">
                <i class="fa-solid fa-spinner"></i>
            </div>
            <div>
                <div class="label">Berjalan</div>
                <div class="value">{{ $berjalan }}</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#F59E0B,#D97706)">
                <i class="fa-solid fa-pause-circle"></i>
            </div>
            <div>
                <div class="label">Nonaktif</div>
                <div class="value">{{ $nonaktif }}</div>
            </div>
        </div>
    </div>

    {{-- Bulk Action Bar --}}
    <form method="POST" action="{{ route('dinkes.periode-survei.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> periode dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-primary"
                    onclick="return confirm('Nonaktifkan semua periode yang dipilih?')">
                <i class="fa-solid fa-pause me-1"></i> Nonaktifkan
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN periode yang dipilih? Periode yang sudah punya data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus
            </button>
        </div>
    </form>

        {{-- Table Card --}}
        <div class="card sp-table-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                            <th>Nama Periode</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th style="width:110px">Status</th>
                            <th style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPeriode as $periode)
                            @php
                                $mulai = $periode->tanggal_mulai;
                                $selesai = $periode->tanggal_selesai;
                                $today = \Carbon\Carbon::now();
                                if ($today->lt($mulai)) {
                                    $statusBadge = 'badge-status-akan-datang';
                                    $statusLabel = 'Akan Datang';
                                } elseif ($today->gt($selesai)) {
                                    $statusBadge = 'badge-status-selesai';
                                    $statusLabel = 'Selesai';
                                } elseif ($periode->is_active) {
                                    $statusBadge = 'badge-status-active';
                                    $statusLabel = 'Aktif';
                                } else {
                                    $statusBadge = 'badge-status-inactive';
                                    $statusLabel = 'Nonaktif';
                                }
                            @endphp
                            <tr>
                                <td><input type="checkbox" data-id="{{ $periode->id }}" class="cek-item"></td>
                                <td class="fw-semibold" style="color:var(--purple-900)">{{ $periode->nama }}</td>
                                <td>{{ $mulai->format('d M Y') }}</td>
                                <td>{{ $selesai->format('d M Y') }}</td>
                                <td>
                                    <span class="{{ $statusBadge }}">{{ $statusLabel }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('dinkes.laporan.index') }}?periode_survei_id={{ $periode->id }}"
                                           class="sp-icon-btn success" title="Rekap Laporan">
                                            <i class="fa-solid fa-chart-column"></i>
                                        </a>
                                        <button type="button" class="sp-icon-btn btn-edit-periode" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#modalPeriode"
                                                data-action="{{ route('dinkes.periode-survei.update', $periode) }}"
                                                data-nama="{{ $periode->nama }}"
                                                data-mulai="{{ $mulai->format('Y-m-d') }}"
                                                data-selesai="{{ $selesai->format('Y-m-d') }}"
                                                data-active="{{ $periode->is_active ? 1 : 0 }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('dinkes.periode-survei.destroy', $periode) }}" method="POST"
                                              onsubmit="return confirm('Hapus periode ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sp-icon-btn danger" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-inbox d-block mb-2" style="font-size:1.5rem;opacity:.3"></i>
                                    Belum ada data periode survei.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- Pagination --}}
    <div class="mt-3 d-flex justify-content-end">
        {{ $daftarPeriode->links('pagination::bootstrap-5') }}
    </div>

    {{-- ============ Modal: Tambah / Edit Periode Survei ============ --}}
    <div class="modal fade sp-modal" id="modalPeriode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formPeriode" action="{{ route('dinkes.periode-survei.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="inputMethod" disabled>

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPeriodeTitle">Tambah Periode Survei</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Quick Pick: Triwulan --}}
                        <div id="blokQuarterPick">
                            <div class="sp-section-label">Pilih Triwulan (Template Otomatis)</div>
                            <div class="sp-quarter-chips" id="quarterChips">
                                <button type="button" class="sp-quarter-chip" data-q="1" onclick="pickQuarter(1)">
                                    <i class="fa-solid fa-arrow-right"></i> TW I
                                </button>
                                <button type="button" class="sp-quarter-chip" data-q="2" onclick="pickQuarter(2)">
                                    <i class="fa-solid fa-arrow-right"></i> TW II
                                </button>
                                <button type="button" class="sp-quarter-chip" data-q="3" onclick="pickQuarter(3)">
                                    <i class="fa-solid fa-arrow-right"></i> TW III
                                </button>
                                <button type="button" class="sp-quarter-chip" data-q="4" onclick="pickQuarter(4)">
                                    <i class="fa-solid fa-arrow-right"></i> TW IV
                                </button>
                            </div>
                            <div class="sp-date-range" id="dateRangePreview" style="display:none">
                                <div>
                                    <div class="label">Rentang Tanggal</div>
                                    <div class="value" id="dateRangeText"></div>
                                </div>
                            </div>
                        </div>

                        <div class="sp-section-label">Informasi Periode</div>

                        <div class="mb-3">
                            <label class="form-label">Nama periode</label>
                            <input type="text" name="nama" id="inputNama" class="form-control"
                                   placeholder="contoh: Triwulan IV 2026" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal mulai</label>
                                <input type="date" name="tanggal_mulai" id="inputTanggalMulai" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal selesai</label>
                                <input type="date" name="tanggal_selesai" id="inputTanggalSelesai" class="form-control" required>
                            </div>
                        </div>

                        <div class="sp-section-label">Status</div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="inputActive">
                            <label class="form-check-label" for="inputActive">Jadikan periode aktif</label>
                        </div>
                        <small class="text-muted d-block mt-1">Periode aktif lain otomatis akan dinonaktifkan.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanPeriode">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modalPeriode = document.getElementById('modalPeriode');
        const formPeriode = document.getElementById('formPeriode');
        const inputMethod = document.getElementById('inputMethod');
        const modalPeriodeTitle = document.getElementById('modalPeriodeTitle');
        const btnSimpanPeriode = document.getElementById('btnSimpanPeriode');
        const STORE_URL = "{{ route('dinkes.periode-survei.store') }}";
        const CURRENT_YEAR = new Date().getFullYear();
        const CURRENT_QUARTER = Math.ceil((new Date().getMonth() + 1) / 3);

        /**
         * Return quarter start/end dates for given quarter and year.
         */
        function getQuarterDates(q, year) {
            const starts = [null, `${year}-01-01`, `${year}-04-01`, `${year}-07-01`, `${year}-10-01`];
            const ends   = [null, `${year}-03-31`, `${year}-06-30`, `${year}-09-30`, `${year}-12-31`];
            return { start: starts[q], end: ends[q] };
        }

        /**
         * Format date string to "d MMMM yyyy" in Indonesian.
         */
        function formatIndoDate(dateStr) {
            const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const parts = dateStr.split('-');
            return `${parseInt(parts[2])} ${months[parseInt(parts[1])]} ${parts[0]}`;
        }

        /**
         * Auto-fill form fields when a quarter chip is clicked.
         */
        function pickQuarter(q) {
            const dates = getQuarterDates(q, CURRENT_YEAR);
            const names = ['', 'Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV'];

            document.getElementById('inputNama').value = `${names[q]} ${CURRENT_YEAR}`;
            document.getElementById('inputTanggalMulai').value = dates.start;
            document.getElementById('inputTanggalSelesai').value = dates.end;

            // Update chip active state
            document.querySelectorAll('.sp-quarter-chip').forEach(chip => {
                chip.classList.toggle('active', parseInt(chip.dataset.q) === q);
            });

            // Show date range preview
            const preview = document.getElementById('dateRangePreview');
            preview.style.display = 'flex';
            document.getElementById('dateRangeText').textContent =
                `${formatIndoDate(dates.start)} — ${formatIndoDate(dates.end)}`;
        }

        /**
         * Open modal for adding new period with auto-suggested values.
         */
        function bukaModalTambah() {
            formPeriode.reset();
            formPeriode.action = STORE_URL;
            inputMethod.disabled = true;
            inputMethod.value = '';
            modalPeriodeTitle.textContent = 'Tambah Periode Survei';
            btnSimpanPeriode.textContent = 'Simpan';
            document.getElementById('blokQuarterPick').style.display = 'block';

            // Auto-highlight current quarter and fill values
            pickQuarter(CURRENT_QUARTER);
        }

        /**
         * Open modal for editing existing period (no auto-template).
         */
        modalPeriode.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger || !trigger.classList.contains('btn-edit-periode')) return;

            formPeriode.action = trigger.dataset.action;
            inputMethod.disabled = false;
            inputMethod.value = 'PUT';
            modalPeriodeTitle.textContent = 'Edit ' + trigger.dataset.nama;
            btnSimpanPeriode.textContent = 'Simpan perubahan';

            document.getElementById('inputNama').value = trigger.dataset.nama || '';
            document.getElementById('inputTanggalMulai').value = trigger.dataset.mulai || '';
            document.getElementById('inputTanggalSelesai').value = trigger.dataset.selesai || '';
            document.getElementById('inputActive').checked = trigger.dataset.active === '1';

            // Hide quarter chips when editing (manual input)
            document.getElementById('blokQuarterPick').style.display = 'none';
        });

        // Bulk selection
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

            // Inject selected IDs as hidden inputs before submit
            document.getElementById('form-aksi-massal').addEventListener('submit', function (event) {
                var cek = document.querySelectorAll('.cek-item:checked');
                if (cek.length === 0) {
                    event.preventDefault();
                    alert('Pilih minimal satu periode untuk diproses.');
                    return;
                }
                this.querySelectorAll('input[name="dipilih[]"]').forEach(function (el) { el.remove(); });
                cek.forEach(function (cb) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'dipilih[]';
                    input.value = cb.dataset.id;
                    this.appendChild(input);
                }.bind(this));
            });
        })();
    </script>

@endsection
