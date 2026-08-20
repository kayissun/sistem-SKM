@extends('layouts.dinkes')

@section('title', 'Periode Survei')

@section('content')

    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: #180733; margin: 0; }
        .sp-page-head p { margin: 2px 0 0; color: #635C7A; font-size: .88rem; }

        .sp-filter-card { border-radius: 14px; }
        .sp-filter-card .input-group-text { background: #fff; border-right: none; color: #9CA3AF; }
        .sp-filter-card .form-control, .sp-filter-card .form-select { border-left: none; }
        .sp-filter-card .input-group:focus-within .input-group-text,
        .sp-filter-card .input-group:focus-within .form-control { border-color: #A78BFA; }

        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table-responsive { scrollbar-width: thin; scrollbar-color: #C4B5FD transparent; }
        .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-track { background: transparent; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: #FAF8FF; }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .6rem .85rem; white-space: nowrap; }
        .sp-table-card thead th { font-size: .72rem; }

        .badge-status-active   { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-weight: 600; padding: .4em .75em; border-radius: 99px; }
        .badge-status-inactive { background: #F3F1FA; color: #6B6480; border: 1px solid #E4DEF7; font-weight: 600; padding: .4em .75em; border-radius: 99px; }

        .sp-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid rgba(109,40,217,.15);
            background: #fff; color: #6D28D9;
            transition: .15s;
        }
        .sp-icon-btn:hover { background: #6D28D9; color: #fff; border-color: #6D28D9; }
        .sp-icon-btn.btn-delete:hover { background: #DC2626; color: #fff; border-color: #DC2626; }

        .sp-bulkbar {
            display: none;
            align-items: center;
            gap: 12px;
            background: #FAF8FF;
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

        .sp-pagination { display: flex; justify-content: flex-end; }
        .sp-pagination nav { margin: 0; }
        .sp-pagination .pagination { gap: 4px; margin: 0; }
        .sp-pagination .page-link {
            border: 1px solid rgba(109,40,217,.12);
            color: #180733;
            border-radius: 8px !important;
            font-size: .82rem;
            font-weight: 600;
            padding: .4rem .7rem;
        }
        .sp-pagination .page-link:hover { background: #F3EEFF; color: #2E1065; }
        .sp-pagination .page-item.active .page-link {
            background: linear-gradient(135deg,#7C3AED,#2A0B5E);
            border-color: transparent;
            color: #fff;
        }
        .sp-pagination .page-item.disabled .page-link { color: #C4BFD6; background: #fff; border-color: rgba(109,40,217,.08); }
    </style>

    <div class="sp-page-head">
        <div>
            <h3>Periode Survei</h3>
            <p>{{ $daftarPeriode->total() }} periode terdaftar dalam jadwal survei.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPeriode"
                onclick="bukaModalTambah()">
            <i class="fa-solid fa-plus me-1"></i> Tambah Periode
        </button>
    </div>

    <!-- Form & Tabel utama dengan Bulk Bar -->
    <form method="POST" action="{{ route('dinkes.periode-survei.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> periode dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-primary"
                    onclick="return confirm('Nonaktifkan semua periode yang dipilih?')">
                <i class="fa-solid fa-pause me-1"></i> Nonaktifkan Terpilih
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN periode yang dipilih? Periode yang sudah punya data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus Terpilih
            </button>
        </div>

        <div class="card sp-table-card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                            <th>No</th>
                            <th>Nama Periode</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th style="width:100px">Status</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPeriode as $periode)
                            <tr>
                                <td><input type="checkbox" name="dipilih[]" value="{{ $periode->id }}" class="cek-item"></td>
                                <td>{{ $loop->iteration + ($daftarPeriode->currentPage() - 1) * $daftarPeriode->perPage() }}</td>
                                <td class="fw-semibold" style="color:#180733">{{ $periode->nama }}</td>
                                <td>{{ $periode->tanggal_mulai->format('d M Y') }}</td>
                                <td>{{ $periode->tanggal_selesai->format('d M Y') }}</td>
                                <td>
                                    @if ($periode->is_active)
                                        <span class="badge-status-active">Aktif</span>
                                    @else
                                        <span class="badge-status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="sp-icon-btn btn-edit-periode" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#modalPeriode"
                                                data-action="{{ route('dinkes.periode-survei.update', $periode) }}"
                                                data-nama="{{ $periode->nama }}"
                                                data-mulai="{{ $periode->tanggal_mulai->format('Y-m-d') }}"
                                                data-selesai="{{ $periode->tanggal_selesai->format('Y-m-d') }}"
                                                data-active="{{ $periode->is_active ? 1 : 0 }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('dinkes.periode-survei.destroy', $periode) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus periode ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sp-icon-btn btn-delete" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data periode survei
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <div class="mt-3 sp-pagination">
        {{ $daftarPeriode->links('pagination::bootstrap-5') }}
    </div>

    <!-- ============ Modal: Tambah / Edit Periode Survei ============ -->
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
                        <div class="sp-section-label">Informasi Periode</div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama periode</label>
                            <input type="text" name="nama" id="inputNama" class="form-control" placeholder="contoh: Triwulan IV 2026" required>
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

        function bukaModalTambah() {
            formPeriode.reset();
            formPeriode.action = STORE_URL;
            inputMethod.disabled = true;
            inputMethod.value = '';
            modalPeriodeTitle.textContent = 'Tambah Periode Survei';
            btnSimpanPeriode.textContent = 'Simpan';
        }

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

            document.getElementById('form-aksi-massal').addEventListener('submit', function (event) {
                if (document.querySelectorAll('.cek-item:checked').length === 0) {
                    event.preventDefault();
                    alert('Pilih minimal satu periode untuk diproses.');
                }
            });
        })();
    </script>

@endsection