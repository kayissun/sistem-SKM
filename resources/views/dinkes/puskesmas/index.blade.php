@extends('layouts.dinkes')

@section('title', 'Puskesmas / RSU')

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
            <h3>Puskesmas / RSU</h3>
            <p>{{ $daftarPuskesmas->total() }} unit terdaftar dalam jaringan layanan.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUnit"
                onclick="bukaModalTambah()">
            <i class="fa-solid fa-plus me-1"></i> Tambah Unit
        </button>
    </div>

    <!-- card pencarian & filter -->
    <div class="card sp-filter-card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('dinkes.puskesmas.index') }}">
                <div class="row g-3 align-items-end">
                    <!-- Field Pencarian -->
                    <div class="col-lg-5 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-muted" style="background:#FAF8FF">
                                <i class="fa-solid fa-search"></i>
                            </span>
                            <input type="text" name="cari" value="{{ $pencarian }}"
                                class="form-control border-start-0 ps-0"
                                style="background:#FAF8FF"
                                placeholder="Nama, alamat, atau kecamatan...">
                        </div>
                    </div>

                    <!-- Field Urutan -->
                    <div class="col-lg-3 col-md-6">
                        <select name="urutan" class="form-select" style="background-color:#FAF8FF">
                            <option value="az" @selected($urutan === 'az')>A ke Z (Ascending)</option>
                            <option value="za" @selected($urutan === 'za')>Z ke A (Descending)</option>
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="col-lg-4 col-md-12 ms-auto">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 fw-medium">
                                <i class="fa-solid fa-filter me-1"></i> Terapkan
                            </button>
                            <a href="{{ route('dinkes.puskesmas.index') }}" class="btn btn-light text-secondary border w-100 fw-medium">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('dinkes.puskesmas.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unit dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-primary"
                    onclick="return confirm('Nonaktifkan semua unit yang dipilih?')">
                <i class="fa-solid fa-pause me-1"></i> Nonaktifkan Terpilih
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN unit yang dipilih? Unit yang sudah punya data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus Terpilih
            </button>
        </div>

        <div class="card sp-table-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Alamat</th>
                            <th>Kecamatan</th>
                            <th>No. Telepon</th>
                            <th>Jumlah akun</th>
                            <th>Email Admin</th>
                            <th>Status</th>
                            <th style="width:150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPuskesmas as $item)
                            @php $admin = $item->users->first(fn ($u) => $u->hasRole('admin-puskesmas')); @endphp
                            <tr>
                                <td><input type="checkbox" name="dipilih[]" value="{{ $item->id }}" class="cek-item"></td>
                                <td>{{ $loop->iteration + ($daftarPuskesmas->currentPage() - 1) * $daftarPuskesmas->perPage() }}</td>
                                <td class="fw-semibold" style="color:#180733">{{ $item->nama }}</td>
                                <td>{{ strtoupper($item->jenis) }}</td>
                                <td>{{ $item->alamat ?? '-' }}</td>
                                <td>{{ $item->kecamatan ?? '-' }}</td>
                                <td>{{ $item->no_telepon ?? '-' }}</td>
                                <td>{{ $item->users_count }}</td>
                                <td>{{ $admin->email ?? '-' }}</td>
                                <td>
                                    @if ($item->is_active)
                                        <span class="badge-status-active">Aktif</span>
                                    @else
                                        <span class="badge-status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="sp-icon-btn btn-edit-unit" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#modalUnit"
                                                data-action="{{ route('dinkes.puskesmas.update', $item) }}"
                                                data-nama="{{ $item->nama }}"
                                                data-jenis="{{ $item->jenis }}"
                                                data-telepon="{{ $item->no_telepon }}"
                                                data-alamat="{{ $item->alamat }}"
                                                data-kecamatan="{{ $item->kecamatan }}"
                                                data-active="{{ $item->is_active ? 1 : 0 }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <a href="{{ route('survei.create', $item) }}" target="_blank" class="sp-icon-btn" title="Link survei">
                                            <i class="fa-solid fa-link"></i>
                                        </a>
                                        @if ($item->is_active)
                                            <a href="{{ route('qrcode.unduh', $item) }}" class="sp-icon-btn" title="Unduh QR">
                                                <i class="fa-solid fa-qrcode"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
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
        </div>
    </form>

    <div class="mt-3 sp-pagination">
        {{ $daftarPuskesmas->links('pagination::bootstrap-5') }}
    </div>

    <!-- ============ Modal: Tambah / Edit Unit ============ -->
    <div class="modal fade sp-modal" id="modalUnit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formUnit" action="{{ route('dinkes.puskesmas.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="inputMethod" disabled>

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUnitTitle">Tambah Puskesmas / RSU</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="sp-section-label">Data Unit</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nama unit</label>
                                <input type="text" name="nama" id="inputNama" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jenis</label>
                                <select name="jenis" id="inputJenis" class="form-select" required>
                                    <option value="puskesmas">Puskesmas</option>
                                    <option value="rsu">RSU</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No. telepon</label>
                                <input type="text" name="no_telepon" id="inputTelepon" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="alamat" id="inputAlamat" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" name="kecamatan" id="inputKecamatan" class="form-control">
                            </div>
                        </div>

                        <div id="blokAkunAdmin">
                            <div class="sp-section-label">Akun Admin Unit</div>
                            <p class="text-muted small mb-3">Password sementara akan dibuat otomatis dan ditampilkan setelah unit tersimpan.</p>
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Nama admin</label>
                                    <input type="text" name="admin_nama" id="inputAdminNama" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email admin</label>
                                    <input type="email" name="admin_email" id="inputAdminEmail" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div id="blokStatusAktif" class="d-none">
                            <div class="sp-section-label">Status</div>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="inputActive">
                                <label class="form-check-label" for="inputActive">Unit aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanUnit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modalUnit = document.getElementById('modalUnit');
        const formUnit = document.getElementById('formUnit');
        const inputMethod = document.getElementById('inputMethod');
        const blokAkunAdmin = document.getElementById('blokAkunAdmin');
        const blokStatusAktif = document.getElementById('blokStatusAktif');
        const modalUnitTitle = document.getElementById('modalUnitTitle');
        const btnSimpanUnit = document.getElementById('btnSimpanUnit');
        const STORE_URL = "{{ route('dinkes.puskesmas.store') }}";

        function bukaModalTambah() {
            formUnit.reset();
            formUnit.action = STORE_URL;
            inputMethod.disabled = true;
            inputMethod.value = '';
            modalUnitTitle.textContent = 'Tambah Puskesmas / RSU';
            btnSimpanUnit.textContent = 'Simpan';
            blokAkunAdmin.classList.remove('d-none');
            blokStatusAktif.classList.add('d-none');
            document.getElementById('inputAdminNama').required = true;
            document.getElementById('inputAdminEmail').required = true;
        }

        modalUnit.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger || !trigger.classList.contains('btn-edit-unit')) return;

            formUnit.action = trigger.dataset.action;
            inputMethod.disabled = false;
            inputMethod.value = 'PUT';
            modalUnitTitle.textContent = 'Edit ' + trigger.dataset.nama;
            btnSimpanUnit.textContent = 'Simpan perubahan';

            document.getElementById('inputNama').value = trigger.dataset.nama || '';
            document.getElementById('inputJenis').value = trigger.dataset.jenis || 'puskesmas';
            document.getElementById('inputTelepon').value = trigger.dataset.telepon || '';
            document.getElementById('inputAlamat').value = trigger.dataset.alamat || '';
            document.getElementById('inputKecamatan').value = trigger.dataset.kecamatan || '';
            document.getElementById('inputActive').checked = trigger.dataset.active === '1';

            document.getElementById('inputAdminNama').required = false;
            document.getElementById('inputAdminEmail').required = false;
            blokAkunAdmin.classList.add('d-none');
            blokStatusAktif.classList.remove('d-none');
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
                    alert('Pilih minimal satu unit untuk diproses.');
                }
            });
        })();
    </script>

@endsection