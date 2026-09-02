@extends('layouts.dinkes')

@section('title', 'Unsur Pelayanan')

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
    </style>

    {{-- Page Header --}}
    <div class="sp-page-head">
        <div>
            <h3>Unsur Pelayanan</h3>
            <p>Master daftar unsur yang membentuk indeks kepuasan masyarakat.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUnsur"
                onclick="bukaModalTambah()">
            <i class="fa-solid fa-plus me-1"></i> Tambah Unsur
        </button>
    </div>

    {{-- Stat Cards --}}
    @php
        $totalUnsur = $daftarUnsur->count();
        $aktif = $daftarUnsur->where('is_active', true)->count();
        $nonaktif = $totalUnsur - $aktif;
    @endphp
    <div class="sp-stat-row">
        <div class="sp-stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#7C3AED,#5B21B6)">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <div class="label">Total Unsur</div>
                <div class="value">{{ $totalUnsur }}</div>
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
    <form method="POST" action="{{ route('dinkes.unsur-pelayanan.aksi-massal') }}" id="form-aksi-massal">
        @csrf

        <div class="sp-bulkbar" id="bar-aksi-massal">
            <span class="small text-muted"><strong id="jumlah-terpilih">0</strong> unsur dipilih</span>
            <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-sm btn-outline-primary"
                    onclick="return confirm('Nonaktifkan semua unsur yang dipilih?')">
                <i class="fa-solid fa-pause me-1"></i> Nonaktifkan
            </button>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus PERMANEN unsur yang dipilih? Unsur yang sudah dipakai di pertanyaan survei tidak akan ikut terhapus, cuma dinonaktifkan otomatis.')">
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
                            <th style="width:90px">Kode</th>
                            <th>Nama Unsur</th>
                            <th style="width:80px">Status</th>
                            <th style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarUnsur as $unsur)
                            <tr>
                                <td><input type="checkbox" data-id="{{ $unsur->id }}" class="cek-item"></td>
                                <td class="fw-semibold" style="color:var(--purple-900)">{{ $unsur->kode }}</td>
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
                                                data-id="{{ $unsur->id }}"
                                                data-action="{{ route('dinkes.unsur-pelayanan.update', $unsur) }}"
                                                data-kode="{{ $unsur->kode }}"
                                                data-nama-unsur="{{ $unsur->nama_unsur }}"
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-inbox d-block mb-2" style="font-size:1.5rem;opacity:.3"></i>
                                    Belum ada unsur pelayanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- ============ Modal: Tambah / Edit Unsur ============ --}}
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
                            <div class="col-md-6">
                                <label class="form-label">Kode</label>
                                <input type="text" name="kode" id="inputKode" class="form-control" placeholder="contoh: U10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Urutan tampil</label>
                                <input type="number" name="urutan" id="inputUrutan" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nama Unsur</label>
                                <input type="text" name="nama_unsur" id="inputNamaUnsur" class="form-control" placeholder="Contoh: Kenyamanan Ruang Tunggu" required>
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

        function bukaModalTambah() {
            formUnsur.reset();
            formUnsur.action = STORE_URL;
            inputMethod.disabled = true;
            inputMethod.value = '';
            modalUnsurTitle.textContent = 'Tambah Unsur Pelayanan';
            btnSimpanUnsur.textContent = 'Simpan';
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
            document.getElementById('inputNamaUnsur').value = trigger.dataset.namaUnsur || '';
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

            // Inject selected IDs as hidden inputs before submit
            document.getElementById('form-aksi-massal').addEventListener('submit', function (event) {
                var cek = document.querySelectorAll('.cek-item:checked');
                if (cek.length === 0) {
                    event.preventDefault();
                    alert('Pilih minimal satu unsur untuk diproses.');
                    return;
                }
                // Remove any previously injected hidden inputs
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
