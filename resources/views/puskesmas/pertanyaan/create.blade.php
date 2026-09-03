@extends('layouts.puskesmas')

@section('title', 'Form Builder Pertanyaan Survei')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    /* ===== Form Builder overrides ===== */
    .gform-card {
        transition: all .2s ease-in-out;
        border-left: 5px solid transparent;
        border-radius: 14px;
    }
    .gform-card.active-card {
        border-left-color: #7C3AED;
        box-shadow: 0 8px 24px rgba(109,40,217,.12);
        background: #fff;
    }
    .gform-card.dirty-card {
        border-left-color: #C88719 !important;
        box-shadow: 0 8px 24px rgba(200,135,25,.10);
    }
    .gform-card:hover:not(.active-card) {
        box-shadow: 0 4px 14px rgba(46,16,101,.06);
    }

    .drag-handle {
        cursor: grab;
        opacity: .3;
        transition: opacity .15s;
    }
    .drag-handle:hover { opacity: 1; }

    .gf-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem;
        flex-shrink: 0;
    }
    .gf-icon.purple { background: #EDE9FE; color: #6D28D9; }
    .gf-icon.gold   { background: #FCF1DC; color: #A66A0E; }
    .gf-icon.green  { background: #ECFDF5; color: #059669; }
    .gf-icon.red    { background: #FEE2E2; color: #B91C1C; }

    .gf-action-btn {
        width: 30px; height: 30px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid rgba(109,40,217,.12);
        background: #fff; color: #6B6480;
        font-size: .82rem;
        transition: .15s;
    }
    .gf-action-btn:hover { background: #FAF8FF; color: #6D28D9; border-color: rgba(109,40,217,.25); }
    .gf-action-btn.danger { color: #B91C1C; border-color: rgba(185,28,28,.15); }
    .gf-action-btn.danger:hover { background: #FEF2F2; color: #B91C1C; border-color: rgba(185,28,28,.3); }

    .gf-question-num {
        width: 30px; height: 30px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 10px;
        background: linear-gradient(135deg, #7C3AED, #2A0B5E);
        color: #fff;
        font-weight: 800; font-size: .78rem;
    }

    .gf-add-btn {
        border: 1.5px dashed #C4B5FD;
        background: transparent;
        color: #7C3AED;
        border-radius: 99px;
        padding: 6px 18px;
        font-weight: 700;
        font-size: .8rem;
        transition: .15s;
    }
    .gf-add-btn:hover {
        background: #F3EEFF;
        border-color: #7C3AED;
    }

    .gf-badge {
        display: inline-block;
        font-size: .68rem;
        font-weight: 700;
        padding: .3em .7em;
        border-radius: 99px;
    }
    .gf-badge.skala { background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; }
    .gf-badge.teks  { background: #FCF1DC; color: #A66A0E; border: 1px solid #F0DFB2; }

    .gf-scale-label {
        display: block;
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 4px;
    }
</style>

<div x-data="formBuilder()" x-init="initData()" class="pb-5">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="sp-pagehead">
        <div>
            <div class="eyebrow">Form Builder</div>
            <h1><i class="bi bi-file-earmark-text me-2" style="font-size:1.1rem;"></i>Pertanyaan Survei SKM</h1>
        </div>
        <div class="meta">
            <div x-show="isDirty" class="meta-item" style="color:#A66A0E;">
                <i class="fa-solid fa-circle-exclamation"></i> Belum disimpan
            </div>
            <div x-show="saving" class="meta-item">
                <span class="spinner-border spinner-border-sm text-primary" role="status"></span> Menyimpan…
            </div>
            <span x-text="toastMessage" class="gf-badge" :class="toastSuccess ? 'skala' : 'teks'" x-show="toastMessage"
                style="padding:.4em .9em; font-size:.78rem;"></span>
            <a href="{{ route('puskesmas.pertanyaan.index') }}" @click="confirmLeave($event)" class="sp-icon-btn" title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="position-relative">

        {{-- ===== GAMBAR IDENTITAS FORM ===== --}}
        <div class="sp-section-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="gf-icon gold">
                        <i class="bi bi-image"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.92rem; color:#180733;">Gambar Identitas Form</div>
                        <div style="font-size:.76rem; color:#635C7A;">Banner yang tampil di atas form survei responden</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <template x-if="formHeaderImageUrl">
                        <div class="d-flex align-items-center gap-2">
                            <img :src="formHeaderImageUrl" class="rounded" style="height:52px; max-width:180px; object-fit:cover; border:2px solid #E4DEF7;">
                            <button type="button" @click="hapusFormHeader()" class="gf-action-btn danger" title="Hapus gambar identitas">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </template>
                    <label class="gf-action-btn cursor-pointer" title="Upload gambar">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <input type="file" class="d-none" accept="image/*" @change="uploadFormHeader($event)">
                    </label>
                </div>
            </div>
        </div>

        {{-- ===== DAFTAR PERTANYAAN ===== --}}
        <div id="sortable-cards">

            {{-- Empty state --}}
            <template x-if="items.length === 0">
                <div class="sp-empty-state">
                    <i class="bi bi-inbox" style="font-size:2.2rem;"></i>
                    <p class="mb-3">Belum ada pertanyaan survei.</p>
                    <button type="button" @click="tambahKartu()" :disabled="saving" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Buat Pertanyaan Pertama
                    </button>
                </div>
            </template>

            {{-- Loop pertanyaan --}}
            <template x-for="(item, index) in items" :key="item.id">
                <div class="position-relative mb-3">

                    {{-- Nomor urut --}}
                    <div class="position-absolute top-0 start-0 m-2 d-flex align-items-center gap-1" style="z-index:5;">
                        <span class="gf-question-num" x-text="index + 1"></span>
                        <button type="button" @click.stop="geserKartu(index, -1)" :disabled="index === 0 || saving"
                            class="gf-action-btn" title="Geser ke atas">
                            <i class="bi bi-chevron-up"></i>
                        </button>
                        <button type="button" @click.stop="geserKartu(index, 1)" :disabled="index === items.length - 1 || saving"
                            class="gf-action-btn" title="Geser ke bawah">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>

                    {{-- Aksi cepat --}}
                    <div class="position-absolute top-0 end-0 m-2 d-flex gap-1" style="z-index:5;">
                        <button type="button" @click.stop="duplikatItem(item.id)" class="gf-action-btn" title="Duplikat">
                            <i class="bi bi-copy"></i>
                        </button>
                        <button type="button" @click.stop="hapusItem(item.id)" class="gf-action-btn danger" title="Hapus">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    {{-- KARTU PERTANYAAN --}}
                    <div :data-id="item.id"
                        @click="setActive(item.id)"
                        class="card gform-card"
                        :class="{
                            'active-card': activeId === item.id,
                            'bg-white': activeId === item.id,
                            'bg-light bg-opacity-50 border-light': activeId !== item.id,
                            'dirty-card': activeId === item.id && isDirty
                        }">

                        {{-- Drag handle --}}
                        <div class="text-center py-1 border-bottom drag-handle rounded-top" style="background:#FAF8FF;">
                            <i class="bi bi-grip-horizontal" style="color:#C4B5FD; font-size:1rem;"></i>
                        </div>

                        <div class="card-body p-4">

                            {{-- A. EDITING STATE --}}
                            <div x-show="activeId === item.id">

                                {{-- Header Image Banner --}}
                                <template x-if="editForm.header_image_url">
                                    <div class="position-relative mb-3">
                                        <img :src="editForm.header_image_url" class="img-fluid rounded w-100" style="max-height:160px; object-fit:cover;">
                                        <button @click.stop="hapusGambarHeader(item)"
                                            class="btn btn-sm position-absolute top-0 end-0 m-2"
                                            style="background:#B91C1C; color:#fff; border:none; border-radius:8px;">
                                            <i class="bi bi-trash3 me-1"></i> Hapus
                                        </button>
                                    </div>
                                </template>

                                {{-- Teks Pertanyaan & Tipe --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="sp-section-label">Teks Pertanyaan</label>
                                        <input type="text" class="form-control" style="border:1px solid #E4DEF7; border-radius:10px; padding:10px 14px;"
                                            x-model="editForm.teks_pertanyaan" @input="markDirty()"
                                            placeholder="Tuliskan pertanyaan di sini…">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="sp-section-label">Tipe Jawaban</label>
                                        <select class="form-select" style="border-radius:10px; padding:10px 14px;"
                                            x-model="editForm.tipe_input" @change="markDirty()">
                                            <option value="skala">Skala Likert (1–4)</option>
                                            <option value="teks">Teks Isian / Saran Bebas</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Opsi Khusus Tipe Skala --}}
                                <template x-if="editForm.tipe_input === 'skala'">
                                    <div style="background:#FAF8FF; border:1px solid #E4DEF7; border-radius:12px; padding:18px;" class="mb-3">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="sp-section-label">Terkait Unsur SKM</label>
                                                <select class="form-select form-select-sm" style="border-radius:8px;"
                                                    x-model="editForm.unsur_pelayanan_id" @change="terpilihUnsur()">
                                                    <option value="">— Pertanyaan Tambahan —</option>
                                                    <template x-for="unsur in daftarUnsur" :key="unsur.id">
                                                        <option :value="unsur.id" :selected="unsur.id == editForm.unsur_pelayanan_id"
                                                            x-text="unsur.kode + ' — ' + unsur.nama_unsur"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="sp-section-label">Gaya Tampilan</label>
                                                <select class="form-select form-select-sm" style="border-radius:8px;"
                                                    x-model="editForm.gaya_tampilan" @change="markDirty()">
                                                    <option value="radio">Radio Button Vertical</option>
                                                    <option value="dropdown">Dropdown Select</option>
                                                </select>
                                            </div>
                                        </div>

                                        <label class="sp-section-label mb-2">Label Opsi Jawaban Skala (1–4)</label>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <span class="gf-badge skala mb-1">Skala 1</span>
                                                <input type="text" class="form-control form-control-sm" style="border-radius:8px;"
                                                    x-model="editForm.label_skala_1" @input="markDirty()"
                                                    placeholder="Sangat Tidak Baik">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <span class="gf-badge skala mb-1">Skala 2</span>
                                                <input type="text" class="form-control form-control-sm" style="border-radius:8px;"
                                                    x-model="editForm.label_skala_2" @input="markDirty()"
                                                    placeholder="Kurang Baik">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <span class="gf-badge skala mb-1">Skala 3</span>
                                                <input type="text" class="form-control form-control-sm" style="border-radius:8px;"
                                                    x-model="editForm.label_skala_3" @input="markDirty()"
                                                    placeholder="Baik">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <span class="gf-badge skala mb-1">Skala 4</span>
                                                <input type="text" class="form-control form-control-sm" style="border-radius:8px;"
                                                    x-model="editForm.label_skala_4" @input="markDirty()"
                                                    placeholder="Sangat Baik">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Footer Actions --}}
                                <div class="d-flex justify-content-between align-items-center pt-3" style="border-top:1px solid #E4DEF7;">
                                    <div>
                                        <label class="gf-action-btn cursor-pointer" title="Upload gambar header">
                                            <i class="bi bi-image"></i>
                                            <input type="file" class="d-none" accept="image/*" @change="uploadGambarHeader($event, item)">
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button x-show="isDirty" @click="saveActiveCard()"
                                            class="btn btn-primary btn-sm" style="border-radius:8px; padding:6px 14px;">
                                            <i class="bi bi-check-lg me-1"></i> Simpan
                                        </button>
                                        <button x-show="isDirty" @click="resetActiveCard()"
                                            class="btn btn-outline-secondary btn-sm" style="border-radius:8px; padding:6px 14px;">
                                            Batal
                                        </button>

                                        <div class="vr mx-1" style="height:24px;"></div>

                                        <button @click.stop="duplikatItem(item.id)" class="gf-action-btn" title="Duplikat">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                        <button @click.stop="hapusItem(item.id)" class="gf-action-btn danger" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>

                                        <div class="vr mx-1" style="height:24px;"></div>

                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox"
                                                :id="'active-' + item.id" x-model="editForm.is_active" @change="markDirty()">
                                            <label class="form-check-label small fw-bold" :for="'active-' + item.id"
                                                x-text="editForm.is_active ? 'Aktif' : 'Nonaktif'"
                                                :style="editForm.is_active ? 'color:#A66A0E' : 'color:#6B6480'"></label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tambah Soal --}}
                                <div class="text-center mt-3">
                                    <button type="button" @click="tambahKartu(index)" :disabled="saving" class="gf-add-btn">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Soal di Bawah Ini
                                    </button>
                                </div>
                            </div>

                            {{-- B. PREVIEW STATE --}}
                            <div x-show="activeId !== item.id" class="d-flex justify-content-between align-items-start">
                                <div style="min-width:0;">
                                    <h5 class="fw-bold mb-1" style="color:#180733; font-size:.95rem;"
                                        x-text="(index + 1) + '. ' + (item.teks_pertanyaan || 'Pertanyaan Tanpa Judul')"></h5>

                                    <template x-if="item.header_image_url">
                                        <img :src="item.header_image_url" class="img-thumbnail my-2" style="max-height:64px;">
                                    </template>

                                    <div class="d-flex gap-2 align-items-center mt-2">
                                        <span class="gf-badge" :class="item.tipe_input === 'teks' ? 'teks' : 'skala'"
                                            x-text="item.tipe_input === 'teks' ? 'Teks Isian' : 'Skala Likert (1-4)'"></span>

                                        <template x-if="item.unsur_pelayanan">
                                            <span class="gf-badge skala" x-text="item.unsur_pelayanan.kode"></span>
                                        </template>

                                        <span x-text="item.is_active ? 'Aktif' : 'Nonaktif'"
                                            :class="item.is_active ? 'badge-status-active' : 'badge-status-inactive'"
                                            style="font-size:.68rem;"></span>
                                    </div>
                                </div>
                                <i class="bi bi-pencil" style="color:#C4B5FD;"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>

<script>
    function formBuilder() {
        return {
            items: [],
            daftarUnsur: @json($daftarUnsur),
            presetLabel: @json($presetLabel),
            activeId: null,
            editForm: {},
            formHeaderImageUrl: @json($formHeaderImageUrl),
            isDirty: false,
            saving: false,
            toastMessage: '',
            toastSuccess: true,

            initData() {
                this.items = @json($daftarPertanyaan);
                this.items.forEach(item => {
                    item.header_image_url = item.header_image ? '{{ asset("storage") }}/' + item.header_image : null;
                });

                if (this.items.length > 0) {
                    this.setActive(this.items[0].id);
                }

                window.addEventListener('beforeunload', (e) => {
                    if (this.isDirty) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                });

                this.$nextTick(() => {
                    this.initSortable();
                });
            },

            setActive(id) {
                if (this.activeId === id) return;

                if (this.isDirty) {
                    if (!confirm('Ada perubahan yang belum disimpan di pertanyaan ini. Yakin ingin berpindah tanpa menyimpan?')) {
                        return;
                    }
                }

                this.activeId = id;
                const activeItem = this.items.find(i => i.id === id);
                this.editForm = JSON.parse(JSON.stringify(activeItem));
                this.isDirty = false;
            },

            markDirty() {
                this.isDirty = true;
            },

            confirmLeave(event) {
                if (this.isDirty) {
                    if (!confirm('Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?')) {
                        event.preventDefault();
                    }
                }
            },

            resetActiveCard() {
                const activeItem = this.items.find(i => i.id === this.activeId);
                this.editForm = JSON.parse(JSON.stringify(activeItem));
                this.isDirty = false;
                this.showToast('Perubahan dibatalkan', true);
            },

            showToast(msg, isSuccess = true) {
                this.toastMessage = msg;
                this.toastSuccess = isSuccess;
                setTimeout(() => {
                    this.toastMessage = '';
                }, 3000);
            },

            initSortable() {
                const el = document.getElementById('sortable-cards');
                if (!el) return;

                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: (evt) => {
                        const newOrderIds = Array.from(el.querySelectorAll('.gform-card')).map(card => parseInt(card.getAttribute('data-id')));
                        this.items.sort((a, b) => newOrderIds.indexOf(a.id) - newOrderIds.indexOf(b.id));
                        this.saveReorder(newOrderIds);
                    }
                });
            },

            async apiFetch(url, options = {}) {
                try {
                    const response = await fetch(url, {
                        ...options,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
                            ...(options.headers || {})
                        }
                    });

                    const text = await response.text();
                    let data = {};
                    try { data = text ? JSON.parse(text) : {}; } catch (e) {
                        throw new Error('Server mengembalikan respons tidak valid (HTTP ' + response.status + ').');
                    }

                    if (!response.ok) {
                        const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                        throw new Error(firstError || data.message || 'Terjadi kesalahan pada server (HTTP ' + response.status + ').');
                    }
                    return data;
                } catch (error) {
                    if (error.name === 'TypeError') {
                        throw new Error('Koneksi ke server gagal/terputus. Cek koneksi internet Anda.');
                    }
                    throw error;
                }
            },

            async geserKartu(index, arah) {
                const target = index + arah;
                if (target < 0 || target >= this.items.length || this.saving) return;

                if (this.isDirty && !confirm('Ada perubahan belum disimpan. Lanjutkan menggeser pertanyaan?')) {
                    return;
                }

                const items = [...this.items];
                const [moved] = items.splice(index, 1);
                items.splice(target, 0, moved);
                this.items = items;
                this.isDirty = false;

                await this.$nextTick();
                this.saveReorder(this.items.map(item => item.id));
            },

            async saveReorder(ids) {
                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.reorder') }}", {
                        method: 'POST',
                        body: JSON.stringify({ urutan: ids })
                    });
                    if (data.success) {
                        this.showToast('Urutan berhasil diperbarui');
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                }
            },

            async tambahKartu(afterIndex) {
                if (this.saving) return;

                if (this.isDirty && !confirm('Ada perubahan belum disimpan. Lanjutkan menambah pertanyaan?')) {
                    return;
                }

                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.store') }}", {
                        method: 'POST',
                        body: JSON.stringify({
                            teks_pertanyaan: 'Pertanyaan Tanpa Judul',
                            tipe_input: 'skala',
                            gaya_tampilan: 'radio',
                            urutan: this.items.length + 1,
                            is_active: true
                        })
                    });

                    const newItem = data.data;

                    if (afterIndex !== undefined && afterIndex !== null) {
                        this.items.splice(afterIndex + 1, 0, newItem);
                    } else {
                        this.items.push(newItem);
                    }

                    this.isDirty = false;
                    this.setActive(newItem.id);
                    this.showToast('Soal baru ditambahkan');
                    this.saveReorder(this.items.map(item => item.id));
                } catch (error) {
                    this.showToast(error.message || 'Gagal menambahkan pertanyaan.', false);
                } finally {
                    this.saving = false;
                }
            },

            async saveActiveCard() {
                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.update', ':id') }}".replace(':id', this.editForm.id), {
                        method: 'PUT',
                        body: JSON.stringify(this.editForm)
                    });
                    if (data.success) {
                        const index = this.items.findIndex(i => i.id === this.editForm.id);
                        if (index !== -1) {
                            this.items[index] = JSON.parse(JSON.stringify(data.data));
                        }
                        this.isDirty = false;
                        this.showToast('Pertanyaan berhasil disimpan');
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                }
            },

            terpilihUnsur() {
                if (this.editForm.unsur_pelayanan_id) {
                    const unsurObj = this.daftarUnsur.find(u => u.id == this.editForm.unsur_pelayanan_id);
                    if (unsurObj && this.presetLabel[unsurObj.kode.toLowerCase()]) {
                        const preset = this.presetLabel[unsurObj.kode.toLowerCase()].label;
                        this.editForm.label_skala_1 = preset[0];
                        this.editForm.label_skala_2 = preset[1];
                        this.editForm.label_skala_3 = preset[2];
                        this.editForm.label_skala_4 = preset[3];
                    }
                }
                this.markDirty();
            },

            async duplikatItem(id) {
                if (this.isDirty && !confirm('Ada perubahan belum disimpan. Lanjutkan duplikasi?')) {
                    return;
                }

                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.duplikat', ':id') }}".replace(':id', id), {
                        method: 'POST'
                    });
                    if (data.success) {
                        this.items.push(data.data);
                        this.isDirty = false;
                        this.setActive(data.data.id);
                        this.showToast('Pertanyaan diduplikat');
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                }
            },

            async uploadGambarHeader(event, item) {
                const file = event.target.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    this.showToast('Ukuran gambar maksimal 2MB.', false);
                    return;
                }

                const formData = new FormData();
                formData.append('header_image', file);

                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.update-header-image', ':id') }}".replace(':id', item.id), {
                        method: 'POST',
                        body: formData
                    });
                    if (data.success) {
                        const url = data.data.header_image_url;
                        this.editForm.header_image_url = url;
                        this.editForm.header_image = data.data.header_image;
                        const index = this.items.findIndex(i => i.id === item.id);
                        if (index !== -1) {
                            this.items[index] = { ...this.items[index], header_image_url: url, header_image: data.data.header_image };
                            this.editForm = JSON.parse(JSON.stringify(this.items[index]));
                        }
                        this.showToast('Gambar header berhasil diunggah');
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                }
            },

            async hapusGambarHeader(item) {
                const formData = new FormData();
                formData.append('hapus_header_image', 1);

                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.update-header-image', ':id') }}".replace(':id', item.id), {
                        method: 'POST',
                        body: formData
                    });
                    if (data.success) {
                        this.editForm.header_image_url = null;
                        const index = this.items.findIndex(i => i.id === item.id);
                        if (index !== -1) {
                            this.items[index].header_image_url = null;
                        }
                        this.showToast('Gambar header dihapus');
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                }
            },

            async uploadFormHeader(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    this.showToast('Ukuran gambar maksimal 2MB.', false);
                    return;
                }

                const formData = new FormData();
                formData.append('form_header_image', file);

                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.form-header-upload') }}", {
                        method: 'POST',
                        body: formData
                    });
                    if (data.success) {
                        this.formHeaderImageUrl = data.data.form_header_image_url;
                        this.showToast(data.message);
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                    event.target.value = '';
                }
            },

            async hapusFormHeader() {
                if (!confirm('Yakin ingin menghapus gambar identitas form?')) return;

                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.form-header-hapus') }}", {
                        method: 'DELETE'
                    });
                    if (data.success) {
                        this.formHeaderImageUrl = null;
                        this.showToast(data.message);
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                }
            },

            async hapusItem(id) {
                if (!confirm('Yakin ingin menghapus pertanyaan ini?')) return;

                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.destroy', ':id') }}".replace(':id', id), {
                        method: 'DELETE'
                    });
                    if (data.deactivated) {
                        const item = this.items.find(i => i.id === id);
                        if (item) item.is_active = false;
                        this.showToast(data.message, false);
                    } else if (data.success) {
                        this.isDirty = false;
                        this.items = this.items.filter(i => i.id !== id);
                        if (this.items.length > 0) {
                            this.setActive(this.items[0].id);
                        }
                        this.showToast('Pertanyaan dihapus');
                    }
                } catch (error) {
                    this.showToast(error.message, false);
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
@endsection
