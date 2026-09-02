@extends('layouts.dinkes')

@section('title', 'Data Faskes')

@section('content')

    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: var(--purple-900); margin: 0; }
        .sp-page-head p { margin: 4px 0 0; color: var(--ink-muted); font-size: .85rem; }

        .sp-filter-card { border: 1px solid rgba(24,7,51,.06); border-radius: 14px; background: #fff; }
        .sp-filter-card .input-group-text { background: #fff; border-right: none; color: #9CA3AF; }
        .sp-filter-card .form-control,
        .sp-filter-card .form-select { background: var(--surface-1); }
        .sp-filter-card .input-group:focus-within .input-group-text,
        .sp-filter-card .input-group:focus-within .form-control { border-color: #A78BFA; }

        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table-responsive { scrollbar-width: thin; scrollbar-color: #C4B5FD transparent; }
        .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-track { background: transparent; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: var(--surface-1); }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .65rem .9rem; white-space: nowrap; }
        .sp-table-card thead th { font-size: .7rem; }
        .sp-table-card td.sp-wrap { white-space: normal; }

        .badge-status-active   { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .76rem; }
        .badge-status-inactive { background: #F3F1FA; color: #6B6480; border: 1px solid #E4DEF7; font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .76rem; }

        .sp-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid rgba(109,40,217,.15);
            background: #fff; color: #6D28D9; text-decoration: none;
            transition: .15s;
        }
        .sp-icon-btn:hover { background: #6D28D9; color: #fff; border-color: #6D28D9; }

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

    {{-- Page Header --}}
    <div class="sp-page-head">
        <div>
            <h3>Data Faskes</h3>
            <p>{{ $daftarPuskesmas->total() }} unit terdaftar dalam jaringan layanan.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUnit"
                onclick="bukaModalTambah()">
            <i class="fa-solid fa-plus me-1"></i> Tambah Unit
        </button>
    </div>

    {{-- Filter Card --}}
    <div class="card sp-filter-card shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('dinkes.puskesmas.index') }}">
                <div class="row g-3 align-items-end">
                    {{-- Search --}}
                    <div class="col-lg-5 col-md-6">
                        <label class="form-label fw-semibold" style="font-size:.78rem;color:var(--ink-muted)">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="fa-solid fa-search"></i>
                            </span>
                            <input type="text" name="cari" value="{{ $pencarian }}"
                                class="form-control border-start-0 ps-0"
                                placeholder="Nama, alamat, atau kecamatan...">
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold" style="font-size:.78rem;color:var(--ink-muted)">Urutkan</label>
                        <select name="urutan" class="form-select">
                            <option value="az" @selected($urutan === 'az')>A ke Z (Ascending)</option>
                            <option value="za" @selected($urutan === 'za')>Z ke A (Descending)</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-lg-3 col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                            </button>
                            <a href="{{ route('dinkes.puskesmas.index') }}" class="btn btn-outline-secondary flex-fill">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Action Bar --}}
    <form method="POST" action="{{ route('dinkes.puskesmas.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unit dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-primary"
                    onclick="return confirm('Nonaktifkan semua unit yang dipilih?')">
                <i class="fa-solid fa-pause me-1"></i> Nonaktifkan
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN unit yang dipilih? Unit yang sudah punya data survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus
            </button>
        </div>

        {{-- Table Card --}}
        <div class="card sp-table-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                            <th style="width:44px">No</th>
                            <th>Nama</th>
                            <th style="width:90px">Jenis</th>
                            <th>Alamat</th>
                            <th style="width:120px">Kecamatan</th>
                            <th style="width:110px">No. Telepon</th>
                            <th style="width:70px">Akun</th>
                            <th>Email Admin</th>
                            <th style="width:80px">Status</th>
                            <th style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPuskesmas as $item)
                            @php $admin = $item->users->first(fn ($u) => $u->hasRole('admin-puskesmas')); @endphp
                            <tr>
                                <td><input type="checkbox" name="dipilih[]" value="{{ $item->id }}" class="cek-item"></td>
                                <td class="text-muted">{{ $loop->iteration + ($daftarPuskesmas->currentPage() - 1) * $daftarPuskesmas->perPage() }}</td>
                                <td class="fw-semibold" style="color:var(--purple-900)">{{ $item->nama }}</td>
                                <td><span class="text-uppercase" style="font-size:.78rem;font-weight:600;color:var(--ink-muted)">{{ $item->jenis }}</span></td>
                                <td class="sp-wrap">{{ $item->alamat ?? '-' }}</td>
                                <td>{{ $item->kecamatan ?? '-' }}</td>
                                <td>{{ $item->no_telepon ?? '-' }}</td>
                                <td class="text-center">{{ $item->users_count }}</td>
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
                                <td colspan="11" class="text-center text-muted py-4">
                                    @if ($pencarian)
                                        Tidak ada unit yang cocok dengan pencarian "<strong>{{ $pencarian }}</strong>".
                                    @else
                                        Belum ada data faskes.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    {{-- Pagination --}}
    <div class="mt-3 sp-pagination">
        {{ $daftarPuskesmas->links('pagination::bootstrap-5') }}
    </div>

    {{-- ============ Modal: Tambah / Edit Unit ============ --}}
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
