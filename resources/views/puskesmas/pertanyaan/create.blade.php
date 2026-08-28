@extends('layouts.puskesmas')

@section('title', 'Form Builder Pertanyaan Survei')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    .gform-card {
        transition: all 0.2s ease-in-out;
        border-left: 6px solid transparent;
    }
    .gform-card.active-card {
        border-left-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .gform-card.dirty-card {
        border-left-color: #ffc107 !important; /* Warna kuning jika ada perubahan belum disimpan */
    }
    .drag-handle {
        cursor: grab;
        opacity: 0.3;
    }
    .drag-handle:hover {
        opacity: 1;
    }
    .floating-toolbar {
        position: sticky;
        top: 20px;
        z-index: 10;
    }
</style>

<div x-data="formBuilder()" x-init="initData()" class="container-fluid pb-5">

    <!-- Header & Tombol Kembali -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-primary me-2"></i>Form Builder Pertanyaan SKM</h3>
            <p class="text-muted small mb-0">Klik kartu untuk mengedit. Tekan <strong>Simpan Pertanyaan</strong> setelah mengubah data.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span x-show="isDirty" class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Ada perubahan belum disimpan</span>
            <span x-show="saving" class="spinner-border spinner-border-sm text-primary" role="status"></span>
            <span x-text="toastMessage" class="badge" :class="toastSuccess ? 'bg-success' : 'bg-danger'" x-show="toastMessage"></span>

            <a href="{{ route('puskesmas.pertanyaan.index') }}" @click="confirmLeave($event)" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="row g-4 position-relative">

        <!-- Main Form Area -->
        <div class="col-lg-10">
            <div id="sortable-cards" class="space-y-3">

                <!-- 1. JIKA BELUM ADA SOAL SAMA SEKALI -->
                <template x-if="items.length === 0">
                    <div class="text-center p-5 border border-dashed rounded bg-white">
                        <p class="text-muted mb-3">Belum ada pertanyaan survei.</p>
                        <button type="button" @click="tambahKartu()" :disabled="saving" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="bi bi-plus-lg me-1"></i> Buat Pertanyaan Pertama
                        </button>
                    </div>
                </template>

                <!-- 2. LOOP DAFTAR PERTANYAAN -->
                <template x-for="(item, index) in items" :key="item.id">
                    <div class="position-relative mb-3">

                        <!-- Nomor urut & tombol geser atas/bawah -->
                        <div class="position-absolute top-0 start-0 m-2 d-flex align-items-center gap-1" style="z-index: 5;">
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6" x-text="index + 1"></span>
                            <button type="button" @click.stop="geserKartu(index, -1)" :disabled="index === 0 || saving" class="btn btn-sm btn-light border shadow-sm" title="Geser ke Atas">
                                <i class="bi bi-arrow-up"></i>
                            </button>
                            <button type="button" @click.stop="geserKartu(index, 1)" :disabled="index === items.length - 1 || saving" class="btn btn-sm btn-light border shadow-sm" title="Geser ke Bawah">
                                <i class="bi bi-arrow-down"></i>
                            </button>
                        </div>

                        <!-- Tombol aksi cepat (selalu tampil di pojok kanan atas kartu) -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-1" style="z-index: 5;">
                            <button type="button" @click.stop="duplikatItem(item.id)" class="btn btn-sm btn-light border shadow-sm" title="Duplikat">
                                <i class="bi bi-files"></i>
                            </button>
                            <button type="button" @click.stop="hapusItem(item.id)" class="btn btn-sm btn-light border shadow-sm text-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <!-- KARTU PERTANYAAN -->
                        <div
                            :data-id="item.id"
                            @click="setActive(item.id)"
                            class="card gform-card border-1"
                            :class="{
                                'active-card bg-white': activeId === item.id,
                                'bg-light border-light': activeId !== item.id,
                                'dirty-card': activeId === item.id && isDirty
                            }"
                        >
                            <!-- Drag Handle -->
                            <div class="text-center py-1 bg-light border-bottom drag-handle rounded-top">
                                <i class="bi bi-grip-horizontal fs-6"></i>
                            </div>

                            <div class="card-body p-4">

                                <!-- A. STATE EDITING (AKTIF) -->
                                <div x-show="activeId === item.id">

                                    <!-- Header Image Banner -->
                                    <template x-if="editForm.header_image_url">
                                        <div class="position-relative mb-3">
                                            <img :src="editForm.header_image_url" class="img-fluid rounded w-100" style="max-height: 180px; object-fit: cover;">
                                            <button @click.stop="hapusGambarHeader(item)" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2">
                                                <i class="bi bi-trash"></i> Hapus Gambar
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Teks Pertanyaan & Dropdown Tipe -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label text-muted small fw-bold">Teks Pertanyaan</label>
                                            <input
                                                type="text"
                                                class="form-control form-control-lg border-0 border-bottom rounded-0 bg-light"
                                                x-model="editForm.teks_pertanyaan"
                                                @input="markDirty()"
                                                placeholder="Tuliskan pertanyaan di sini..."
                                            >
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-muted small fw-bold">Tipe Jawaban</label>
                                            <select class="form-select" x-model="editForm.tipe_input" @change="markDirty()">
                                                <option value="skala">Skala Likert (1 - 4)</option>
                                                <option value="teks">Teks Isian/Saran Bebas</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Opsi Khusus Tipe Skala -->
                                    <template x-if="editForm.tipe_input === 'skala'">
                                        <div class="bg-light p-3 rounded mb-3 border">
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">Terkait Unsur SKM Wajib</label>
                                                    <select class="form-select form-select-sm" x-model="editForm.unsur_pelayanan_id" @change="terpilihUnsur()">
                                                        <option value="">-- Pertanyaan Tambahan (Tanpa Unsur) --</option>
                                                        <template x-for="unsur in daftarUnsur" :key="unsur.id">
                                                            <option :value="unsur.id" :selected="unsur.id == editForm.unsur_pelayanan_id" x-text="unsur.kode + ' - ' + unsur.nama_unsur"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">Gaya Tampilan Skala</label>
                                                    <select class="form-select form-select-sm" x-model="editForm.gaya_tampilan" @change="markDirty()">
                                                        <option value="radio">Radio Button Vertical</option>
                                                        <option value="dropdown">Dropdown Select</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <label class="form-label text-muted small fw-bold mb-2">Label Opsi Jawaban Skala (1 - 4):</label>
                                            <div class="row g-2">
                                                <div class="col-6 col-md-3">
                                                    <span class="badge bg-secondary mb-1">Skala 1</span>
                                                    <input type="text" class="form-control form-control-sm" x-model="editForm.label_skala_1" @input="markDirty()" placeholder="Sangat Tidak Baik">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <span class="badge bg-secondary mb-1">Skala 2</span>
                                                    <input type="text" class="form-control form-control-sm" x-model="editForm.label_skala_2" @input="markDirty()" placeholder="Kurang Baik">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <span class="badge bg-secondary mb-1">Skala 3</span>
                                                    <input type="text" class="form-control form-control-sm" x-model="editForm.label_skala_3" @input="markDirty()" placeholder="Baik">
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <span class="badge bg-secondary mb-1">Skala 4</span>
                                                    <input type="text" class="form-control form-control-sm" x-model="editForm.label_skala_4" @input="markDirty()" placeholder="Sangat Baik">
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Footer Actions Kartu Aktif -->
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <label class="small text-muted mb-0">Layout Mode:</label>
                                            <select class="form-select form-select-sm w-auto" x-model="editForm.layout_mode" @change="markDirty()">
                                                <option value="default">Default</option>
                                                <option value="stacked">Stacked Card</option>
                                                <option value="separated">Separated Block</option>
                                            </select>

                                            <label class="btn btn-sm btn-outline-secondary mb-0 cursor-pointer">
                                                <i class="bi bi-image"></i> Upload Gambar
                                                <input type="file" class="d-none" accept="image/*" @change="uploadGambarHeader($event, item)">
                                            </label>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <!-- Tombol Simpan & Batal Manual -->
                                            <button x-show="isDirty" @click="saveActiveCard()" class="btn btn-sm btn-primary">
                                                <i class="bi bi-check-circle me-1"></i> Simpan Pertanyaan
                                            </button>
                                            <button x-show="isDirty" @click="resetActiveCard()" class="btn btn-sm btn-outline-secondary">
                                                Batal
                                            </button>

                                            <div class="vr mx-1"></div>

                                            <button @click.stop="duplikatItem(item.id)" class="btn btn-sm btn-link text-muted p-0" title="Duplikat"><i class="bi bi-files fs-5"></i></button>
                                            <button @click.stop="hapusItem(item.id)" class="btn btn-sm btn-link text-danger p-0" title="Hapus"><i class="bi bi-trash fs-5"></i></button>
                                            <div class="vr mx-1"></div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" :id="'active-' + item.id" x-model="editForm.is_active" @change="markDirty()">
                                                <label class="form-check-label small fw-bold" :for="'active-' + item.id" x-text="editForm.is_active ? 'Aktif' : 'Nonaktif'"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- B. STATE PREVIEW (UNFOCUSED / READ-ONLY) -->
                                <div x-show="activeId !== item.id">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="fw-bold mb-1" x-text="(index + 1) + '. ' + (item.teks_pertanyaan || 'Pertanyaan Tanpa Judul')"></h5>

                                            <template x-if="item.header_image_url">
                                                <img :src="item.header_image_url" class="img-thumbnail my-2" style="max-height: 80px;">
                                            </template>

                                            <div class="d-flex gap-2 align-items-center mt-2">
                                                <span class="badge" :class="item.tipe_input === 'teks' ? 'bg-info text-dark' : 'bg-secondary'" x-text="item.tipe_input === 'teks' ? 'Teks Isian' : 'Skala Likert (1-4)'"></span>

                                                <template x-if="item.unsur_pelayanan">
                                                    <span class="badge bg-primary" x-text="item.unsur_pelayanan.kode"></span>
                                                </template>

                                                <span class="badge" :class="item.is_active ? 'bg-success' : 'bg-danger'" x-text="item.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                            </div>
                                        </div>
                                        <i class="bi bi-pencil text-muted"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- Floating Side Menu -->
        <div class="col-lg-2">
            <div class="floating-toolbar card shadow-sm p-2 text-center bg-white border">
                <p class="fw-bold small mb-2 text-start"><i class="bi bi-patch-check text-primary me-1"></i> Identitas Form</p>
                <template x-if="formHeaderImageUrl">
                    <div class="position-relative mb-2">
                        <img :src="formHeaderImageUrl" class="img-fluid rounded border" style="max-height: 90px; object-fit: cover;">
                        <button type="button" @click="hapusFormHeader()" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" title="Hapus gambar identitas">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </template>
                <label class="btn btn-sm btn-outline-primary w-100 mb-2 cursor-pointer">
                    <i class="bi bi-image me-1"></i> <span x-text="formHeaderImageUrl ? 'Ganti Gambar' : 'Upload Gambar'"></span>
                    <input type="file" class="d-none" accept="image/*" @change="uploadFormHeader($event)">
                </label>
                <div class="form-check form-switch text-start small mb-2 border-top pt-2">
                    <input class="form-check-input" type="checkbox" id="pisahHalamanSwitch" x-model="pisahHalaman" @change="simpanPisahHalaman()">
                    <label class="form-check-label" for="pisahHalamanSwitch">
                        Pisah halaman<br><span class="text-muted" style="font-size: 0.75em;">Data diri terpisah dari pertanyaan</span>
                    </label>
                </div>
                <button type="button" @click="tambahKartu()" :disabled="saving" class="btn btn-primary btn-sm w-100 mb-2 py-2">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Kartu
                </button>
                <p class="text-muted text-xs mb-0">Total: <strong x-text="items.length"></strong> Soal</p>
            </div>
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
            pisahHalaman: @json($pisahHalaman),
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
                        // sinkronkan array items dengan urutan DOM hasil drag
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
                        // Laravel kirim errors dalam bentuk object validasi
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

            async tambahKartu() {
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
                    this.items.push(newItem);
                    this.isDirty = false;
                    this.setActive(newItem.id);
                    this.showToast('Kartu baru ditambahkan');
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
                        // sinkronkan juga di array items agar preview kartu ikut berubah
                        const index = this.items.findIndex(i => i.id === item.id);
                        if (index !== -1) {
                            this.items[index] = { ...this.items[index], header_image_url: url, header_image: data.data.header_image };
                            // muat ulang editForm agar banner pratinjau langsung tampil
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

            async simpanPisahHalaman() {
                this.saving = true;
                try {
                    const data = await this.apiFetch("{{ route('puskesmas.pertanyaan.pengaturan-form') }}", {
                        method: 'POST',
                        body: JSON.stringify({ pisah_halaman: this.pisahHalaman })
                    });
                    if (data.success) {
                        this.showToast(data.message);
                    }
                } catch (error) {
                    this.pisahHalaman = !this.pisahHalaman;
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