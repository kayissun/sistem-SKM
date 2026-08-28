@extends('layouts.puskesmas')

@section('title', 'Form Builder Pertanyaan Survei')

@section('content')
<!-- Import Alpine.js & SortableJS via CDN (jika belum ada di layout) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    /* Styling khusus Google Forms Focus Card */
    .gform-card {
        transition: all 0.2s ease-in-out;
        border-left: 6px solid transparent;
    }
    .gform-card.active-card {
        border-left-color: #0d6efd; /* Warna biru primer Google Form */
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

    /* Container untuk divider interaktif antar kartu */
    .insert-divider {
        position: relative;
        height: 24px;
        margin: -6px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }

    /* Tampilkan garis & tombol saat area di-hover */
    .insert-divider:hover {
        opacity: 1;
    }

    /* Garis horisontal pemisah */
    .insert-divider::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        height: 2px;
        background-color: #0d6efd; /* Warna garis aksen biru */
        z-index: 1;
    }

    /* Tombol + di tengah garis */
    .insert-divider .btn-insert {
        position: relative;
        z-index: 2;
        border-radius: 50px;
        padding: 2px 14px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);
    }
</style>

<div x-data="formBuilder()" x-init="initData()" class="container-fluid pb-5">

    <!-- Header & Status Toast -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-primary me-2"></i>Form Builder Pertanyaan SKM</h3>
            <p class="text-muted small mb-0">Klik pada kartu untuk mengedit. Geser ikon titik untuk mengubah urutan.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span x-show="saving" class="spinner-border spinner-border-sm text-primary" role="status"></span>
            <span x-text="toastMessage" class="badge" :class="toastSuccess ? 'bg-success' : 'bg-danger'" x-show="toastMessage"></span>
        </div>
    </div>

    <!-- Alert Unsur Wajib Belum Ada -->
    @if ($unsurBelumAda->isNotEmpty())
        <div class="alert alert-warning shadow-sm border-0 mb-4">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <strong>Perhatian Unsur SKM Wajib:</strong>
            </div>
            <p class="small mb-1">Puskesmas ini belum memiliki pertanyaan aktif untuk unsur berikut. Nilai SKM kurang akurat tanpa pertanyaan aktif di unsur ini:</p>
            <div class="d-flex flex-wrap gap-1 mt-2">
                @foreach ($unsurBelumAda as $unsur)
                    <span class="badge bg-dark">{{ $unsur->kode }} - {{ $unsur->nama_unsur }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-4 position-relative">
        
        <!-- Daftar Kartu Pertanyaan (Main Form Area) -->
        <div class="col-lg-10">
            <div id="sortable-cards" class="space-y-3">
                <template x-for="(item, index) in items" :key="item.id">
                    <div 
                        :data-id="item.id"
                        @click="setActive(item.id)"
                        class="card gform-card mb-3 border-1"
                        :class="activeId === item.id ? 'active-card bg-white' : 'bg-light border-light'"
                    >
                        <!-- Drag Handle -->
                        <div class="text-center py-1 bg-light border-bottom drag-handle rounded-top">
                            <i class="bi bi-grip-horizontal fs-6"></i>
                        </div>

                        <div class="card-body p-4">
                            
                            <!-- STATE EDITING (AKTIF) -->
                            <div x-show="activeId === item.id">
                                
                                <!-- Header Image Banner jika Ada -->
                                <template x-if="item.header_image_url">
                                    <div class="position-relative mb-3">
                                        <img :src="item.header_image_url" class="img-fluid rounded w-100" style="max-height: 180px; object-fit: cover;">
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
                                            x-model="item.teks_pertanyaan" 
                                            @change="saveItem(item)"
                                            placeholder="Tuliskan pertanyaan di sini..."
                                        >
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted small fw-bold">Tipe Jawaban</label>
                                        <select class="form-select" x-model="item.tipe_input" @change="saveItem(item)">
                                            <option value="skala">Skala Likert (1 - 4)</option>
                                            <option value="teks">Teks Isian/Saran Bebas</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Opsi Khusus Tipe Skala -->
                                <template x-if="item.tipe_input === 'skala'">
                                    <div class="bg-light p-3 rounded mb-3 border">
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Terkait Unsur SKM Wajib</label>
                                                <select class="form-select form-select-sm" x-model="item.unsur_pelayanan_id" @change="terpilihUnsur(item)">
                                                    <option value="">-- Pertanyaan Tambahan (Tanpa Unsur) --</option>
                                                    <template x-for="unsur in daftarUnsur" :key="unsur.id">
                                                        <option :value="unsur.id" :selected="unsur.id == item.unsur_pelayanan_id" x-text="unsur.kode + ' - ' + unsur.nama_unsur"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Gaya Tampilan Skala</label>
                                                <select class="form-select form-select-sm" x-model="item.gaya_tampilan" @change="saveItem(item)">
                                                    <option value="radio">Radio Button Vertical</option>
                                                    <option value="dropdown">Dropdown Select</option>
                                                </select>
                                            </div>
                                        </div>

                                        <label class="form-label text-muted small fw-bold mb-2">Label Opsi Jawaban Skala (1 - 4):</label>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <span class="badge bg-secondary mb-1">Skala 1</span>
                                                <input type="text" class="form-control form-control-sm" x-model="item.label_skala_1" @change="saveItem(item)" placeholder="Sangat Tidak Baik">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <span class="badge bg-secondary mb-1">Skala 2</span>
                                                <input type="text" class="form-control form-control-sm" x-model="item.label_skala_2" @change="saveItem(item)" placeholder="Kurang Baik">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <span class="badge bg-secondary mb-1">Skala 3</span>
                                                <input type="text" class="form-control form-control-sm" x-model="item.label_skala_3" @change="saveItem(item)" placeholder="Baik">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <span class="badge bg-secondary mb-1">Skala 4</span>
                                                <input type="text" class="form-control form-control-sm" x-model="item.label_skala_4" @change="saveItem(item)" placeholder="Sangat Baik">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Footer Actions Kartu Aktif -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="small text-muted mb-0">Layout Mode:</label>
                                        <select class="form-select form-select-sm w-auto" x-model="item.layout_mode" @change="saveItem(item)">
                                            <option value="default">Default</option>
                                            <option value="stacked">Stacked Card</option>
                                            <option value="separated">Separated Block</option>
                                        </select>
                                        
                                        <!-- Tombol Upload Gambar Header -->
                                        <label class="btn btn-sm btn-outline-secondary mb-0 cursor-pointer">
                                            <i class="bi bi-image"></i> Upload Gambar
                                            <input type="file" class="d-none" accept="image/*" @change="uploadGambarHeader($event, item)">
                                        </label>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <button @click.stop="duplikatItem(item.id)" class="btn btn-sm btn-link text-muted p-0" title="Duplikat"><i class="bi bi-files fs-5"></i></button>
                                        <button @click.stop="hapusItem(item.id)" class="btn btn-sm btn-link text-danger p-0" title="Hapus"><i class="bi bi-trash fs-5"></i></button>
                                        <div class="vr"></div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" :id="'active-' + item.id" x-model="item.is_active" @change="saveItem(item)">
                                            <label class="form-check-label small fw-bold" :for="'active-' + item.id" x-text="item.is_active ? 'Aktif' : 'Nonaktif'"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STATE PREVIEW (UNFOCUSED / READ-ONLY) -->
                            <div x-show="activeId !== item.id">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="fw-bold mb-1" x-text="(index + 1) + '. ' + (item.teks_pertanyaan || 'Pertanyaan Tanpa Judul')"></h5>
                                        
                                        <!-- Banner Preview Mini -->
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
                </template>
            </div>

            <template x-if="items.length === 0">
                <div class="card text-center p-5 border-dashed">
                    <p class="text-muted">Belum ada pertanyaan survei. Klik tombol "+ Tambah Pertanyaan" di sebelah kanan.</p>
                </div>
            </template>
        </div>

        <!-- Floating Action Menu (Side Toolbar) -->
        <div class="col-lg-2">
            <div class="floating-toolbar card shadow-sm p-2 text-center bg-white border">
                <button @click="tambahItem()" class="btn btn-primary btn-sm w-100 mb-2 py-2">
                    <i class="bi bi-plus-circle me-1"></i> Tambah
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
            saving: false,
            toastMessage: '',
            toastSuccess: true,

            initData() {
                this.items = @json($daftarPertanyaan);
                this.items.forEach(item => {
                    item.header_image_url = item.header_image ? '{{ asset("storage") }}/' + item.header_image : null;
                });

                if (this.items.length > 0) {
                    this.activeId = this.items[0].id;
                }

                this.$nextTick(() => {
                    this.initSortable();
                });
            },

            setActive(id) {
                this.activeId = id;
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
                        this.saveReorder(newOrderIds);
                    }
                });
            },

            saveReorder(ids) {
                this.saving = true;
                fetch("{{ route('puskesmas.pertanyaan.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ urutan: ids })
                })
                .then(res => res.json())
                .then(data => {
                    this.saving = false;
                    if (data.success) {
                        this.showToast('Urutan berhasil diperbarui');
                    }
                });
            },

            tambahItemIndex(targetIndex) {
                this.saving = true;
                
                fetch("{{ route('puskesmas.pertanyaan.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        teks_pertanyaan: 'Pertanyaan Tanpa Judul',
                        tipe_input: 'skala',
                        gaya_tampilan: 'radio',
                        is_active: true
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.saving = false;
                    if (data.success) {
                        const newItem = data.data;
                        
                        // Sisipkan item baru tepat di urutan targetIndex
                        this.items.splice(targetIndex, 0, newItem);
                        
                        // Set kartu baru sebagai aktif
                        this.activeId = newItem.id;
                        this.showToast('Pertanyaan baru ditambahkan');

                        // Update urutan baru ke database
                        const newOrderIds = this.items.map(item => item.id);
                        this.saveReorder(newOrderIds);
                    }
                });
            },

            saveItem(item) {
                this.saving = true;
                fetch(`/puskesmas/pertanyaan/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(item)
                })
                .then(res => res.json())
                .then(data => {
                    this.saving = false;
                    if (data.success) {
                        this.showToast('Tersimpan');
                    }
                });
            },

            terpilihUnsur(item) {
                if (item.unsur_pelayanan_id) {
                    const unsurObj = this.daftarUnsur.find(u => u.id == item.unsur_pelayanan_id);
                    if (unsurObj && this.presetLabel[unsurObj.kode.toLowerCase()]) {
                        const preset = this.presetLabel[unsurObj.kode.toLowerCase()].label;
                        item.label_skala_1 = preset[0];
                        item.label_skala_2 = preset[1];
                        item.label_skala_3 = preset[2];
                        item.label_skala_4 = preset[3];
                    }
                }
                this.saveItem(item);
            },

            duplikatItem(id) {
                this.saving = true;
                fetch(`/puskesmas/pertanyaan/${id}/duplikat`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.saving = false;
                    if (data.success) {
                        this.items.push(data.data);
                        this.activeId = data.data.id;
                        this.showToast('Pertanyaan diduplikat');
                    }
                });
            },

            uploadGambarHeader(event, item) {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('header_image', file);

                this.saving = true;
                fetch(`/puskesmas/pertanyaan/${item.id}/header-gambar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    this.saving = false;
                    if (data.success) {
                        item.header_image_url = data.data.header_image_url;
                        this.showToast('Gambar header berhasil diunggah');
                    }
                });
            },

            hapusGambarHeader(item) {
                const formData = new FormData();
                formData.append('hapus_header_image', 1);

                this.saving = true;
                fetch(`/puskesmas/pertanyaan/${item.id}/header-gambar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    this.saving = false;
                    if (data.success) {
                        item.header_image_url = null;
                        this.showToast('Gambar header dihapus');
                    }
                });
            },

            hapusItem(id) {
                if (!confirm('Yakin ingin menghapus pertanyaan ini?')) return;

                this.saving = true;
                fetch(`/puskesmas/pertanyaan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.saving = false;
                    if (data.deactivated) {
                        const item = this.items.find(i => i.id === id);
                        if (item) item.is_active = false;
                        this.showToast(data.message, false);
                    } else if (data.success) {
                        this.items = this.items.filter(i => i.id !== id);
                        this.showToast('Pertanyaan dihapus');
                    }
                });
            }
        }
    }
</script>
@endsection