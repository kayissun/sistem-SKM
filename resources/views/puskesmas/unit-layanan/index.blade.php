@extends('layouts.puskesmas')

@section('title', 'Unit Layanan')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    /* ===== Ikon bulat kecil (dipakai di header & tiap baris) ===== */
    .gf-icon {
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .gf-icon.purple { background: var(--purple-100); color: var(--purple-700); }
    .gf-icon-head { width: 34px; height: 34px; font-size: .85rem; }
    .gf-icon-row  { width: 32px; height: 32px; font-size: .78rem; }

    /* ===== Header ===== */
    .ul-subtitle { font-size: .82rem; color: var(--ink-muted); margin: 4px 0 0; }
    .ul-btn-primary {
        border-radius: 10px; padding: 8px 18px; font-weight: 600;
        background: var(--purple-700); border-color: var(--purple-700);
    }
    .ul-btn-primary:hover { background: var(--purple-800); border-color: var(--purple-800); }

    /* ===== Bulk action bar ===== */
    .ul-bulk-check { color: var(--purple-700); }
    .ul-bulk-text { font-size: .82rem; color: var(--ink-muted); }
    .ul-btn-hapus {
        border: 1px solid rgba(185,28,28,.2); background: #FEF2F2; color: #B91C1C;
        border-radius: 8px; font-weight: 600;
    }
    .ul-btn-hapus:hover { background: #B91C1C; color: #fff; }

    /* ===== Table ===== */
    .ul-empty i { color: #C4B5FD; }
    .ul-empty p { color: var(--ink-muted); font-size: .88rem; }
    .ul-table th {
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; color: var(--ink-muted);
    }
    .ul-table td { vertical-align: middle; padding: 12px 16px; border-bottom: 1px solid #E4DEF7; }
    .ul-table tbody tr:last-child td { border-bottom: none; }
    .ul-table tbody tr:hover td { background: var(--surface-1); }
    .ul-table .unit-name { font-weight: 600; color: var(--purple-900); font-size: .88rem; }
    .ul-check { accent-color: var(--purple-700); }
    .sp-icon-btn.danger { color: #DC2626; border-color: rgba(220,38,38,.15); }
    .sp-icon-btn.danger:hover { background: #DC2626; color: #fff; border-color: #DC2626; }

    /* ===== Modal ===== */
    .ul-modal-input { border-radius: 10px; padding: 10px 14px; }
    .ul-modal-hint { font-size: .74rem; color: var(--ink-muted); margin-top: 6px; }
</style>

<div x-data="unitManager()" x-init="init()" class="pb-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="sp-page-head">
        <div>
            <h3>Unit Layanan / Poli</h3>
            <p>
                {{ $daftarUnitLayanan->count() }} unit terdaftar — muncul sebagai pilihan dropdown di form survei publik.
            </p>
        </div>
        <button @click="openCreateModal()" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Tambah Unit
        </button>
    </div>

    {{-- ===== BULK ACTION BAR ===== --}}
    <form method="POST" action="{{ route('puskesmas.unit-layanan.aksi-massal') }}" id="form-aksi-massal">
        @csrf
        <div class="sp-bulkbar" id="bar-aksi-massal" style="display:none;">
            <i class="fa-solid fa-check-double ul-bulk-check"></i>
            <span class="ul-bulk-text"><strong id="jumlah-terpilih">0</strong> unit dipilih</span>
            <button type="submit" name="aksi" value="hapus" class="btn btn-sm ul-btn-hapus"
                onclick="return confirm('Hapus PERMANEN unit layanan yang dipilih? Unit yang sudah dipakai di data survei tidak akan ikut terhapus, hanya dinonaktifkan otomatis.')">
                <i class="fa-solid fa-trash me-1"></i> Hapus
            </button>
        </div>
    </form>

    {{-- ===== TABLE ===== --}}
    <div class="sp-section-card">
        <table class="table mb-0 ul-table table-bordered">
            <thead>
                <tr>
                    <th style="width:40px; padding-left:20px;">
                        <input type="checkbox" id="pilih-semua" class="ul-check">
                    </th>
                    <th>Nama Unit</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:100px; text-align:right; padding-right:20px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarUnitLayanan as $unit)
                    <tr>
                        <td style="padding-left:20px;">
                            <input type="checkbox" name="dipilih[]" value="{{ $unit->id }}" class="cek-item ul-check">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="unit-name">{{ $unit->nama }}</span>
                            </div>
                        </td>
                        <td>
                            @if ($unit->is_active)
                                <span class="badge-status-active">Aktif</span>
                            @else
                                <span class="badge-status-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align:right; padding-right:20px;">
                            <div class="d-flex gap-1 justify-content-end">
                                <button @click="openEditModal({{ $unit->id }}, '{{ addslashes($unit->nama) }}', {{ $unit->is_active ? 'true' : 'false' }})"
                                    class="sp-icon-btn" title="Edit">
                                    <i class="fa-solid fa-pen" style="font-size:.72rem;"></i>
                                </button>
                                <form action="{{ route('puskesmas.unit-layanan.destroy', $unit) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus unit layanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="sp-icon-btn danger" title="Hapus">
                                        <i class="fa-solid fa-trash" style="font-size:.72rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 ul-empty">
                            <i class="fa-regular fa-folder-open d-block mb-2" style="font-size:2rem; opacity:.4;"></i>
                            <p class="mb-3">Belum ada unit layanan.</p>
                            <button @click="openCreateModal()" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-1"></i> Tambah Unit Pertama
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3 sp-pagination">
        {{ $daftarUnitLayanan->links('pagination::bootstrap-5') }}
    </div>

    {{-- ===== MODAL: Tambah Unit ===== --}}
    <div class="modal fade sp-modal" id="modalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-plus me-2"></i> Tambah Unit Layanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('puskesmas.unit-layanan.store') }}">
                    @csrf
                    <div class="modal-body">
                        <label class="sp-section-label">Nama Unit Layanan</label>
                        <input type="text" name="nama" class="form-control ul-modal-input"
                            placeholder="contoh: Poli Gigi, Poli Umum" required>
                        <div class="ul-modal-hint">
                            Nama ini akan tampil di dropdown form survei responden.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== MODAL: Edit Unit ===== --}}
    <div class="modal fade sp-modal" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-pen me-2"></i> Edit Unit Layanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form :action="editUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <label class="sp-section-label">Nama Unit Layanan</label>
                        <input type="text" name="nama" class="form-control ul-modal-input" x-model="editNama" required>

                        <div class="form-check form-switch mt-3">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input ul-check"
                                id="edit_is_active" x-model="editActive">
                            <label class="form-check-label fw-semibold" for="edit_is_active" style="font-size:.85rem;">
                                Aktif (ditampilkan di dropdown survei)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function unitManager() {
        return {
            editUrl: '',
            editNama: '',
            editActive: false,

            init() {
                this.initCheckboxes();
            },

            openCreateModal() {
                const modal = new bootstrap.Modal(document.getElementById('modalCreate'));
                modal.show();
            },

            openEditModal(id, nama, isActive) {
                this.editUrl = "{{ route('puskesmas.unit-layanan.index') }}/" + id;
                this.editNama = nama;
                this.editActive = isActive;
                this.$nextTick(() => {
                    const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
                    modal.show();
                });
            },

            initCheckboxes() {
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
                        alert('Pilih minimal satu unit layanan untuk dihapus.');
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
            }
        }
    }
</script>
@endsection