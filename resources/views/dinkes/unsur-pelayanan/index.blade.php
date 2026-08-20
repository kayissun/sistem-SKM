@extends('layouts.dinkes')

@section('title', 'Unsur Pelayanan')

@section('content')

    <style>
        .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head h3 { font-weight: 800; color: #180733; margin: 0; }
        .sp-page-head p { margin: 2px 0 0; color: #635C7A; font-size: .88rem; }

        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table-responsive { scrollbar-width: thin; scrollbar-color: #C4B5FD transparent; }
        .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-track { background: transparent; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: #FAF8FF; }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .6rem .85rem; white-space: nowrap; }
        .sp-table-card thead th { font-size: .72rem; }
        .sp-table-card td.sp-wrap { white-space: normal; }

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
        .sp-icon-btn.danger { color: #DC2626; }
        .sp-icon-btn.danger:hover { background: #DC2626; color: #fff; border-color: #DC2626; }

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
    </style>

    <div class="sp-page-head">
        <div>
            <h3>Master Unsur Pelayanan</h3>
            <p>{{ $daftarUnsur->count() }} unsur pelayanan terdaftar.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUnsur"
                onclick="bukaModalTambah()">
            <i class="fa-solid fa-plus me-1"></i> Tambah Unsur
        </button>
    </div>

    <form method="POST" action="{{ route('dinkes.unsur-pelayanan.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unsur dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-primary"
                    onclick="return confirm('Nonaktifkan semua unsur yang dipilih?')">
                <i class="fa-solid fa-pause me-1"></i> Nonaktifkan Terpilih
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN unsur yang dipilih? Unsur yang sudah dipakai di pertanyaan survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus Terpilih
            </button>
        </div>

        <div class="card sp-table-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="pilih-semua"></th>
                            <th style="width:70px">Urutan</th>
                            <th style="width:90px">Kode</th>
                            <th>Nama Unsur</th>
                            <th style="width:110px">Status</th>
                            <th style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarUnsur as $unsur)
                            <tr>
                                <td><input type="checkbox" name="dipilih[]" value="{{ $unsur->id }}" class="cek-item"></td>
                                <td>{{ $unsur->urutan }}</td>
                                <td class="fw-semibold" style="color:#180733">{{ $unsur->kode }}</td>
                                <td class="sp-wrap">{{ $unsur->nama_unsur }}</td>
                                <td>
                                    @if ($unsur->is_active)
                                        <span class="badge-status-active">Aktif</span>
                                    @else
                                        <span class="badge-status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="sp-icon-btn btn-edit-unsur" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#modalUnsur"
                                                data-action="{{ route('dinkes.unsur-pelayanan.update', $unsur) }}"
                                                data-kode="{{ $unsur->kode }}"
                                                data-pertanyaan="{{ $unsur->pertanyaan ?? $unsur->nama_unsur }}"
                                                data-urutan="{{ $unsur->urutan }}"
                                                data-active="{{ $unsur->is_active ? 1 : 0 }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('dinkes.unsur-pelayanan.destroy', $unsur) }}" method="POST"
                                              onsubmit="return confirm('Hapus unsur ini?')">
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
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <!-- ============ Modal: Tambah / Edit Unsur ============ -->
    <div class="modal fade sp-modal" id="modalUnsur" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formUnsur" action="{{ route('dinkes.unsur-pelayanan.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="inputMethod" disabled>

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUnsurTitle">Tambah Unsur Pelayanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="sp-section-label">Data Unsur</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Kode</label>
                                <input type="text" name="kode" id="inputKode" class="form-control" placeholder="contoh: U10" required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Urutan tampil</label>
                                <input type="number" name="urutan" id="inputUrutan" class="form-control" min="1" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nama Unsur</label>
                                <input type="text" name="pertanyaan" id="inputPertanyaan" class="form-control" required>
                            </div>
                        </div>

                        <div id="blokStatusAktif" class="d-none">
                            <div class="sp-section-label">Status</div>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="inputActive">
                                <label class="form-check-label" for="inputActive">Aktif (ditampilkan di form survei)</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanUnsur">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modalUnsur = document.getElementById('modalUnsur');
        const formUnsur = document.getElementById('formUnsur');
        const inputMethod = document.getElementById('inputMethod');
        const blokStatusAktif = document.getElementById('blokStatusAktif');
        const modalUnsurTitle = document.getElementById('modalUnsurTitle');
        const btnSimpanUnsur = document.getElementById('btnSimpanUnsur');
        const STORE_URL = "{{ route('dinkes.unsur-pelayanan.store') }}";
        const URUTAN_BERIKUTNYA = {{ (int) ($urutanBerikutnya ?? 1) }};

        function bukaModalTambah() {
            formUnsur.reset();
            formUnsur.action = STORE_URL;
            inputMethod.disabled = true;
            inputMethod.value = '';
            modalUnsurTitle.textContent = 'Tambah Unsur Pelayanan';
            btnSimpanUnsur.textContent = 'Simpan';
            document.getElementById('inputUrutan').value = URUTAN_BERIKUTNYA;
            blokStatusAktif.classList.add('d-none');
        }

        modalUnsur.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger || !trigger.classList.contains('btn-edit-unsur')) return;

            formUnsur.action = trigger.dataset.action;
            inputMethod.disabled = false;
            inputMethod.value = 'PUT';
            modalUnsurTitle.textContent = 'Edit Unsur ' + trigger.dataset.kode;
            btnSimpanUnsur.textContent = 'Simpan perubahan';

            document.getElementById('inputKode').value = trigger.dataset.kode || '';
            document.getElementById('inputPertanyaan').value = trigger.dataset.pertanyaan || '';
            document.getElementById('inputUrutan').value = trigger.dataset.urutan || 1;
            document.getElementById('inputActive').checked = trigger.dataset.active === '1';

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
                    alert('Pilih minimal satu unsur untuk diproses.');
                }
            });
        })();
    </script>

@endsection